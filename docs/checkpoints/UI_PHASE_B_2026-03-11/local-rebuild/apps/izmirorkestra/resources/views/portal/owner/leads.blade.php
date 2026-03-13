@extends('portal.layout')

@section('content')
    <div class="card">
        <h2>Owner Leadler</h2>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Telefon</th>
                    <th>Mesaj</th>
                    <th>Durum</th>
                    <th>Not</th>
                    <th>Aksiyon</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->phone }}</td>
                        <td>{{ $r->message }}</td>
                        <td>{{ $r->status }}</td>
                        <td>{{ $r->internal_note }}</td>
                        <td>
                            <form method="post" action="{{ route('owner.leads.status', ['customerRequest' => $r->id]) }}">
                                @csrf
                                <input type="text" name="internal_note" value="{{ $r->internal_note }}" placeholder="Kisa not">
                                <div class="actions">
                                    <button class="btn" type="submit" name="status" value="contacted">Iletisime Gecildi</button>
                                    <button class="btn" type="submit" name="status" value="closed">Kapat</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Kayit yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-10">{{ $rows->links() }}</div>
    </div>
@endsection
