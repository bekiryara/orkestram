# TASK-035

Durum: `TODO`  
Ajan: `codex-c`  
Branch: `agent/codex-c/task-035`  
Baslangic: `2026-03-15 07:10`

## Ozet
- Ajan hizlandirma icin deterministic dogrulama pipeline check-listi ve kapanis kanit formatini standartlamak.

## In Scope
- [ ] Hangi adimda `validate`, hangi adimda `pre-pr` kosulacagini net tabloyla yazmak.
- [ ] Ajan cevap formati icin zorunlu kanit bloklarini standartlamak.
- [ ] "Uzun bekleme / interrupted" durumunda resume protokolu yazmak.
- [ ] Koordinatora teslim formatini sabitlemek (dosya listesi + kanit + risk).

## Out of Scope
- [ ] Uygulama feature gelistirme
- [ ] UI duzenleme
- [ ] CI altyapi degisikligi

## Lock Dosyalari
- `docs/tasks/TASK-035.md`
- `docs/PR_FLOW_TR.md`
- `docs/AGENT_DELIVERY_CHECKLIST_TR.md`

## Kabul Kriteri
- [ ] Ajanlar icin hizli ama guvenli pipeline adimlari net olur.
- [ ] Zorunlu kanit formati tek tip olur.
- [ ] Resume protokolu belirsizligi giderir.
- [ ] `pre-pr` PASS

## Komutlar
```powershell
powershell -ExecutionPolicy Bypass -File D:\orkestram\scripts\pre-pr.ps1 -Mode quick
```

## Notlar
- Hedef: ajanlarin deneme-yanilma suresini azaltip ilk seferde dogru kanitla ilerlemek.
