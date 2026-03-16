# TASK-032

Durum: `DONE`  
Ajan: `codex`  
Branch: `agent/codex/task-032`  
Baslangic: `2026-03-15 06:50`

## Ozet
- WSL tek-kaynak (single source of truth) uzerinden, koordinator + paralel ajan (codex-a/b/c) modeliyle stabil ve deterministic gelistirme akisinin resmi olarak kurulmasi.
- D:\orkestram sadece edit/export noktasi olacak; calisan runtime ve resmi kanit WSL kopyalarindan alinacak.

## In Scope
- [x] `D:\orkestram` acilisinda Hard Guard kuraliyla WSL'ye zorunlu hizalama protokolunu AGENTS + disiplin dokumanlarina eklemek.
- [x] Koordinator icin tek aktif koordinasyon lock'u acmak (`TASK-032`).
- [x] Ajanlar icin dosya cakismasiz lock parcalama plani cikarmak (A/B/C ayrik file set).
- [x] WSL calisma koklerini standartlamak ve kanitlamak:
  - `/home/bekir/orkestram-a`
  - `/home/bekir/orkestram-b`
  - `/home/bekir/orkestram-c`
- [x] Her ajan icin branch naming + kanit paketi sozlesmesini netlestirmek.
- [x] Komut standartlarini netlestirmek (`dev-up`, `validate`, `pre-pr`) ve sadece WSL kaynakli calistirma zorunlulugunu yazmak.
- [x] Ajan hizlandirma adimlari icin deterministic check-list hazirlamak (komut kisaltmalari, once-kanit sonra-kod akisi, stop/continue kurali).
- [x] Koordinator kapanis protokolunu tanimlamak (lock closure, kanit, devir notu).

## Out of Scope
- [ ] Uygulama is kurali degisikligi (controller/model/refactor).
- [ ] UI/UX degisikligi.
- [ ] Deploy veya production ayari.
- [ ] Runtime topoloji degisikligi disinda altyapi refactor.

## Lock Dosyalari
- `docs/tasks/TASK-032.md`
- `docs/TASK_LOCKS.md`
- `docs/NEXT_TASK.md`
- `docs/WORKLOG.md`
- `docs/REPO_DISCIPLINE_TR.md`
- `docs/MULTI_AGENT_RULES_TR.md`
- `docs/PR_FLOW_TR.md`
- `AGENTS.md`
- `scripts/pre-pr.ps1`
- `scripts/validate.ps1`
- `docs/WSL_RUNTIME_PLAYBOOK_TR.md`
- `docs/AGENT_LOCK_MATRIX_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`

## Kabul Kriteri
- [x] `TASK-032` lock kaydi `active` acilir ve koordinasyon bitisinde `closed` kapanir.
- [x] A/B/C ajanlari icin cakismaz lock matrisi yazilir (dosya/desen bazli).
- [x] WSL tek-kaynak kurali acik, test edilebilir ve kanitlanabilir sekilde dokumante edilir.
- [x] Her ajan icin zorunlu kanit paketi tek formatta yazilir:
  - `git branch --show-current`
  - `git status --short`
  - `pre-pr PASS/FAIL`
- [x] Koordinator devir/teslim adimi dokumanlarda net olur (kimin neyi ne zaman kapatacagi belirsiz kalmaz).
- [x] `powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick` => PASS

## Komutlar
```powershell
# 1) Stack standart kaldirma
powershell -ExecutionPolicy Bypass -File scripts/dev-up.ps1 -App both

# 2) Hizli dogrulama
powershell -ExecutionPolicy Bypass -File scripts/validate.ps1 -App both

# 3) PR oncesi zorunlu kapi
powershell -ExecutionPolicy Bypass -File scripts/pre-pr.ps1 -Mode quick
```

## Notlar
- Kritik ilke: Kodun "gercek" hali WSL mount kaynaklarinda olmalidir; Windows yolu calisan kaynak sayilmaz.
- Paralel ajanlarda ayni dosyaya lock verilmez; koordinator lock dagitimindan sorumludur.
- Ajan calismasi kesilirse veya terminal kapanirsa once branch/lock/kanit dogrulama yapilir, sonra devam edilir.
- Risk: Yanlis mount veya yanlis klasorde calisma. Azaltim: her ajan turu basinda `pwd + git rev-parse --show-toplevel + git branch --show-current` kaniti.
- Koordinator entegrasyon ozeti:
  - `TASK-033` ciktilari `docs/WSL_RUNTIME_PLAYBOOK_TR.md` icinde toplandi.
  - `TASK-034` ciktilari `docs/AGENT_LOCK_MATRIX_TR.md` icinde toplandi.
  - `TASK-035` ciktilari `docs/AGENT_DELIVERY_CHECKLIST_TR.md` icinde toplandi.
- Bu ortamda `wsl -e bash -lc ...` komutu kullanilamadi; ayni hizalama kaniti yerel WSL bash uzerinden `pwd`, `git rev-parse --show-toplevel`, `git branch --show-current`, `git status --short` ile alindi.

## Koordinator Yurutme Plani (Detay)
1. Baslatma:
   - `TASK-032` lock acilir, panoda tek aktif gorev olarak isaretlenir.
2. Parcalama:
   - `codex-a`: runtime/WSL kanitlari + agent startup protokolu.
   - `codex-b`: lock matrisi + dokuman format birligi.
   - `codex-c`: dogrulama pipeline check-list + kapanis protokolu.
3. Entegrasyon:
   - Koordinator sadece lockli dosyalari birlestirir, capraz kontrol eder.
4. Kapanis:
   - `pre-pr` PASS alinmadan closure yok.
   - `TASK_LOCKS` kaydi `closed`, `NEXT_TASK` uygun duruma cekilir, `WORKLOG` final kaydi dusulur.
5. Kanit:
   - Branch/status/pre-pr ciktilari tek mesajda raporlanir.
