# WSL Runtime Playbook (TR)

Tarih: 2026-03-16

Amac:
1. WSL uzerindeki dogru workdir ile calisma baslangicini deterministic hale getirmek.
2. Runtime durdugunda stop/recover adimlarini tek akista toplamak.
3. Her oturum oncesi startup checklisti ile yanlis klasor, yanlis mount ve stale runtime riskini azaltmak.

## 1) Tek Kaynak ve Dogru Workdir

Temel kural:
1. Calisan kaynak dizin `WSL: /home/bekir/orkestram` kabul edilir.
2. Ajan calismasi yalniz ayrilmis worktree klasorlerinde yapilir:
   - `/home/bekir/orkestram-a`
   - `/home/bekir/orkestram-b`
   - `/home/bekir/orkestram-c`
3. Windows yolu `D:\orkestram` runtime icin referans alinmaz; sadece export veya script giris noktasi olabilir.

Workdir secim kurali:
1. Runtime veya mount davranisi dogrulanacaksa referans klasor `/home/bekir/orkestram` olur.
2. Gorev gelistirmesi yapiliyorsa ajan yalniz kendi worktree'sinde calisir.
3. `pwd` sonucu beklenen workdir ile ayni degilse hicbir degisiklik yapilmaz.

## 2) Baslangic Kaniti

Her gorev veya runtime oturumu basinda su 3 kanit alinmadan ise girilmez:
1. `pwd`
2. `git branch --show-current`
3. `git status --short`

Beklenen yorum:
1. `pwd` aktif worktree'yi gostermelidir.
2. `git branch --show-current` `agent/<ajan>/<task-id>` veya hazirlik asamasinda `main` sonucunu vermelidir.
3. `git status --short` beklenmeyen degisiklik veya baska ajana ait kirli alan gostermemelidir.

Ek runtime kaniti:
1. `docker ps --format "table {{.Names}}\t{{.Status}}"` ile container durumu gorulur.
2. `powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both` komutu disinda manuel `docker compose up` kullanilmaz.

## 3) Startup Checklist

Oturum acilis sirasi:
1. `pwd` ile dogru workdir'i dogrula.
2. `git fetch --all --prune` calistir.
3. `git branch --show-current` ile kendi branch'inde oldugunu dogrula.
4. `docs/TASK_LOCKS.md` icinde hedef dosya icin aktif cakisma olmadigini kontrol et.
5. Gerekiyorsa `docs/tasks/TASK-XXX.md` ve lock kaydini ac.
6. `git status --short` ile worktree temizligini dogrula.
7. Runtime gerekiyorsa standart komutla ayağa kaldir:
   - `powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both`
8. Runtime ayaga kalkinca temel durum kanitlarini kaydet:
   - `docker ps`
   - ilgili uygulama smoke sonucu

## 4) Stop Kurali

Stop gerektiren durumlar:
1. Yanlis workdir'de oldugunu fark etmek.
2. Yanlis branch veya baska goreve ait lock cakismasi gormek.
3. Container isimleri beklenen stack ile eslesmemek.
4. Mount kaynagi ile ekranda gorulen kodun farkli oldugundan suphelenmek.

Hemen dur adimlari:
1. Yeni degisiklik yapmayi kes.
2. `git status --short` ciktisini kaydet.
3. `docker ps --format "table {{.Names}}\t{{.Mounts}}\t{{.Status}}"` ile aktif mount bilgisini kontrol et.
4. Gerekirse stack'i standart komutla yeniden kurmadan once sorun notunu yaz.

## 5) Recover Akisi

Yanlis workdir veya mount suphelerinde:
1. `pwd` ve `git branch --show-current` ile aktif klasoru yeniden dogrula.
2. Dogru klasore gec:
   - runtime kaynagi icin `/home/bekir/orkestram`
   - ajan gorevi icin kendi worktree klasorun
3. `git status --short` ile local sapma var mi bak.
4. Runtime'i standart komutla yeniden kur:
   - `powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both`
5. Uygulama smoke veya hedef endpoint kontrolu yap.

Container stale ise:
1. Once isim ve durum kaniti al:
   - `docker ps -a --format "table {{.Names}}\t{{.Status}}"`
2. Sorun devam ediyorsa ekip standardina gore runtime reset uygula.
3. Recovery sonrasi tekrar baslangic kanitlarini yenile.

## 6) Kapanis Kontrolu

Oturum sonunda:
1. `git branch --show-current`
2. `git status --short`
3. Gorev dokumani ve lock durumunu guncelle.
4. Gerekliyse `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` calistir ve sonucu kaydet.
