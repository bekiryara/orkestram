<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use App\Models\ListingFeedback;
use App\Models\ListingLike;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\AdminIdentityResolver;
use App\Services\Portal\MessageCenterService;
use App\Services\Portal\OwnerResourceAccess;
use App\Services\Portal\PortalContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PortalAuthController extends Controller
{
    private const SESSION_KEY = 'admin_identity';

    public function __construct(
        private readonly PortalContext $portalContext,
        private readonly OwnerResourceAccess $ownerResourceAccess,
        private readonly MessageCenterService $messageCenterService
    ) {
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        $next = $this->sanitizeNextPath((string) $request->query('next', ''));
        $identity = $request->session()->get(self::SESSION_KEY);
        if (is_array($identity) && !empty($identity['role'])) {
            if ($next !== '') {
                return redirect($next);
            }
            return redirect($this->homeByRole((string) $identity['role']));
        }

        return view('auth.login', ['next' => $next]);
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        $next = $this->sanitizeNextPath((string) $request->query('next', ''));
        $identity = $request->session()->get(self::SESSION_KEY);
        if (is_array($identity) && !empty($identity['role'])) {
            if ($next !== '') {
                return redirect($next);
            }
            return redirect($this->homeByRole((string) $identity['role']));
        }

        return view('auth.register', ['next' => $next]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customerRole = Role::query()
            ->where('slug', 'customer')
            ->where('is_active', true)
            ->first();

        if ($customerRole === null) {
            return back()
                ->withErrors(['register' => 'Kayit gecici olarak kapali.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = DB::transaction(function () use ($data, $customerRole): User {
            $user = User::query()->create([
                'name' => (string) $data['name'],
                'username' => (string) $data['username'],
                'email' => (string) $data['email'],
                'password' => Hash::make((string) $data['password']),
                'is_active' => true,
            ]);

            $user->roles()->syncWithoutDetaching([$customerRole->id]);

            return $user;
        });

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, [
            'user' => (string) $user->username,
            'role' => 'customer',
            'roles' => ['customer'],
            'user_id' => (int) $user->id,
            'source' => 'register',
        ]);

        $next = $this->sanitizeNextPath((string) $request->input('next', ''));
        if ($next !== '') {
            return redirect()->to($next);
        }

        return redirect()->intended($this->homeByRole('customer'));
    }

    public function login(Request $request, AdminIdentityResolver $resolver): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:190'],
        ]);

        $identity = $resolver->resolveFromCredentials((string) $data['username'], (string) $data['password']);
        if ($identity === null) {
            return back()
                ->withErrors(['username' => 'Kullanici adi veya sifre hatali.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, [
            'user' => (string) ($identity['user'] ?? ''),
            'role' => (string) ($identity['role'] ?? ''),
            'roles' => (array) ($identity['roles'] ?? [((string) ($identity['role'] ?? ''))]),
            'user_id' => isset($identity['user_id']) ? (int) $identity['user_id'] : null,
            'source' => (string) ($identity['source'] ?? 'login'),
        ]);

        $next = $this->sanitizeNextPath((string) $request->input('next', ''));
        if ($next !== '') {
            return redirect()->to($next);
        }

        return redirect()->intended($this->homeByRole((string) $identity['role']));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/giris');
    }

    public function account(Request $request): View|RedirectResponse
    {
        $identity = (array) $request->session()->get(self::SESSION_KEY, []);
        $user = null;
        $userId = (int) ($identity['user_id'] ?? 0);
        $activeRole = (string) ($identity['role'] ?? '');
        $tab = trim((string) $request->query('tab', 'overview'));

        if ($userId > 0) {
            $user = User::query()->find($userId);
        }

        $summaryCards = [];
        $requestRows = null;
        $messageRows = null;
        $messageCenter = null;
        $commentRows = null;
        $summaryCards = $this->summaryCards($request, $activeRole, $userId, $user);
        $requestRows = $this->requestRows($request, $activeRole, $userId);
        $messageRows = $this->feedbackRowsByKind($request, $userId, 'message', 'messages_page');
        $commentRows = $this->feedbackRowsByKind($request, $userId, 'comment', 'comments_page');
        if ($requestRows !== null) {
            $requestRows->appends(['tab' => 'requests']);
        }
        if ($messageRows !== null) {
            $messageRows->appends(['tab' => 'messages']);
        }
        if ($commentRows !== null) {
            $commentRows->appends(['tab' => 'comments']);
        }
        if ($tab === 'messages') {
            $messageCenter = $this->messageCenterService->buildData($request, 'personal');
        }

        return view('portal.account', [
            'identity' => $identity,
            'user' => $user,
            'activeRole' => $activeRole,
            'summaryCards' => $summaryCards,
            'requestRows' => $requestRows,
            'messageRows' => $messageRows,
            'messageCenter' => $messageCenter,
            'commentRows' => $commentRows,
        ]);
    }

    public function panel(Request $request): RedirectResponse
    {
        $identity = (array) $request->session()->get(self::SESSION_KEY, []);
        $role = (string) ($identity['role'] ?? '');

        return redirect($this->homeByRole($role));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $identity = (array) $request->session()->get(self::SESSION_KEY, []);
        $userId = (int) ($identity['user_id'] ?? 0);
        if ($userId <= 0) {
            return back()->withErrors(['profile' => 'Bu hesap tipinde profil guncelleme kapali.']);
        }

        $user = User::query()->findOrFail($userId);
        $activeRole = (string) ($identity['role'] ?? '');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'service_area' => ['nullable', 'string', 'max:255'],
            'short_bio' => ['nullable', 'string', 'max:4000'],
            'provided_services' => ['nullable', 'string', 'max:4000'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'string', 'max:4000'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = (string) $request->file('profile_photo')->store('profile-photos', 'public');
        }

        if ($activeRole !== 'listing_owner') {
            unset($data['company_name'], $data['service_area'], $data['short_bio'], $data['provided_services'], $data['website_url'], $data['social_links']);
        }

        unset($data['profile_photo']);
        $user->update($data);

        $identity['user'] = (string) $user->username;
        $request->session()->put(self::SESSION_KEY, $identity);

        return redirect()
            ->route('auth.account', ['tab' => 'profile'])
            ->with('ok_profile', 'Profil bilgileri guncellendi.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $identity = (array) $request->session()->get(self::SESSION_KEY, []);
        $userId = (int) ($identity['user_id'] ?? 0);
        if ($userId <= 0) {
            return back()->withErrors(['password' => 'Bu hesap tipinde sifre degistirme kapali.']);
        }

        $user = User::query()->findOrFail($userId);
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check((string) $data['current_password'], (string) $user->password)) {
            return back()->withErrors(['password' => 'Mevcut sifre hatali.']);
        }

        $user->update([
            'password' => Hash::make((string) $data['new_password']),
        ]);

        return back()->with('ok_password', 'Sifre basariyla guncellendi.');
    }

    private function homeByRole(string $role): string
    {
        $map = (array) config('role_home.map', []);
        $default = (string) config('role_home.default', '/admin/pages');
        return (string) ($map[$role] ?? $default);
    }

    private function sanitizeNextPath(string $next): string
    {
        $next = trim($next);
        if ($next === '' || !str_starts_with($next, '/')) {
            return '';
        }

        if (str_starts_with($next, '//')) {
            return '';
        }

        $allowedPrefixes = [
            '/customer',
            '/owner',
            '/support',
            '/hesabim',
            '/panel',
            '/admin',
            '/ilan/',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($next, $prefix)) {
                return $next;
            }
        }

        return '';
    }

    /**
     * @return array<int, array{label:string,value:string}>
     */
    private function summaryCards(Request $request, string $activeRole, int $userId, ?User $user): array
    {
        $site = $this->portalContext->site($request);
        $base = CustomerRequest::query()
            ->where('site', $site)
            ->when($userId > 0, fn($q) => $q->where('user_id', $userId), fn($q) => $q->whereRaw('1 = 0'));
        $feedbackBase = ListingFeedback::query()
            ->where('site', $site)
            ->when($userId > 0, fn($q) => $q->where('user_id', $userId), fn($q) => $q->whereRaw('1 = 0'));

        $open = (clone $base)->where('status', '!=', 'closed')->count();
        $messages = (clone $feedbackBase)->where('kind', 'message')->count();
        $comments = (clone $feedbackBase)->where('kind', 'comment')->count();
        $likes = ListingLike::query()
            ->where('site', $site)
            ->when($userId > 0, fn($q) => $q->where('user_id', $userId), fn($q) => $q->whereRaw('1 = 0'))
            ->count();

        return [
            ['label' => 'Acik Taleplerim', 'value' => (string) $open],
            ['label' => 'Mesajlarim', 'value' => (string) $messages],
            ['label' => 'Yorumlarim', 'value' => (string) $comments],
            ['label' => 'Begenilerim', 'value' => (string) $likes],
            ['label' => 'Profil Tamamlanma Orani', 'value' => $this->profileCompletion($user, $activeRole) . '%'],
        ];
    }

    /**
     * @return array<int, array{title:string,description:string,href:string,button:string}>
     */
    private function quickActions(string $activeRole): array
    {
        if ($activeRole === 'listing_owner') {
            return [
                ['title' => 'Gelen Talepleri Gor', 'description' => 'Musterilerden gelen yeni talepleri incele.', 'href' => '/owner/leads', 'button' => 'Gelen Talepleri Gor'],
                ['title' => 'Hizmetlerimi Yonet', 'description' => 'Ilanlarini guncelle ve yeni hizmet ekle.', 'href' => '/owner/listings', 'button' => 'Hizmetlerimi Yonet'],
                ['title' => 'Mesajlarim', 'description' => 'Iletisim akislarini tek yerde takip et.', 'href' => '/owner/leads', 'button' => 'Mesajlarimi Ac'],
                ['title' => 'Profili Duzenle', 'description' => 'Firma ve hesap bilgilerini guncel tut.', 'href' => '/hesabim?tab=profil', 'button' => 'Profili Duzenle'],
            ];
        }

        return [
            ['title' => 'Yeni Talep Olustur', 'description' => 'Etkinligin icin yeni talep gonder.', 'href' => '/customer', 'button' => 'Yeni Talep Olustur'],
            ['title' => 'Taleplerime Git', 'description' => 'Olusturdugun taleplerin durumunu gor.', 'href' => '/customer/requests', 'button' => 'Taleplerime Git'],
            ['title' => 'Mesajlarim', 'description' => 'Teklif ve iletisim hareketlerini takip et.', 'href' => '/customer/requests', 'button' => 'Mesajlarimi Ac'],
            ['title' => 'Profili Duzenle', 'description' => 'Kisisel bilgilerini guncel tut.', 'href' => '/hesabim?tab=profil', 'button' => 'Profili Duzenle'],
        ];
    }

    private function profileCompletion(?User $user, string $activeRole): int
    {
        if ($user === null) {
            return 0;
        }

        $fields = ['name', 'username', 'email', 'phone', 'city', 'district', 'profile_photo_path'];
        if ($activeRole === 'listing_owner') {
            $fields = array_merge($fields, ['company_name', 'service_area', 'short_bio', 'provided_services']);
        }

        $filled = 0;
        foreach ($fields as $field) {
            $value = (string) ($user->{$field} ?? '');
            if (trim($value) !== '') {
                $filled++;
            }
        }

        return (int) round(($filled / max(count($fields), 1)) * 100);
    }

    private function requestRows(Request $request, string $activeRole, int $userId)
    {
        $site = $this->portalContext->site($request);
        return CustomerRequest::query()
            ->where('site', $site)
            ->when($userId > 0, fn($q) => $q->where('user_id', $userId), fn($q) => $q->whereRaw('1=0'))
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    private function feedbackRowsByKind(Request $request, int $userId, string $kind, string $pageName)
    {
        $site = $this->portalContext->site($request);
        return ListingFeedback::query()
            ->where('site', $site)
            ->when($userId > 0, fn($q) => $q->where('user_id', $userId), fn($q) => $q->whereRaw('1=0'))
            ->where('kind', $kind)
            ->with('listing:id,slug,name')
            ->latest()
            ->paginate(10, ['*'], $pageName)
            ->withQueryString();
    }

}
