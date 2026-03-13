# TASK LOCKS

Durumlar:
- `active`: gorev acik, lock var
- `closed`: gorev kapandi, lock bosaldi

Sabit Ajan Kimlikleri:
- `codex-a` = A ajani (yalniz `agent/codex-a/...`)
- `codex-b` = B ajani (yalniz `agent/codex-b/...`)
- `codex` = Koordinator ajani (yalniz `agent/codex/...`)

| task_id | agent | branch | status | files | started_at | updated_at | note |
|---|---|---|---|---|---|---|---|
| TASK-001 | codex | agent/codex/task-001 | closed | docs/NEXT_TASK.md,docs/WORKLOG.md,docs/PROJECT_STATUS_TR.md,docs/REPO_DISCIPLINE_TR.md,docs/MULTI_AGENT_RULES_TR.md,docs/TASK_LOCKS.md,docs/tasks/_TEMPLATE.md,scripts/start-task.ps1 | 2026-03-13 08:41:51 | 2026-03-13 23:30:00 | Multi-agent disiplin seti finalize edildi |
| TASK-009 | codex-b | agent/codex-b/task-009 | closed | docs/tasks/TASK-009.md,docs/SERVICE_AREA_FALLBACK_DARALTMA_PLANI_TR.md | 2026-03-13 10:40:00 | 2026-03-13 11:45:00 | Plan checkliste cevrildi, pre-pr PASS, push tamamlandi |
| TASK-011 | codex-a | agent/codex-a/task-011 | active | docs/tasks/TASK-011.md,scripts/dev-up.ps1,scripts/validate.ps1,docs/RUNTIME_PERMISSION_HARDENING_TR.md | 2026-03-13 11:30:00 | 2026-03-13 11:30:00 | Runtime izin sertlestirme: storage/bootstrap preflight ve startup kaliciligi |
| TASK-012 | codex | agent/codex/task-001 | active | docs/tasks/TASK-012.md,scripts/release.ps1,scripts/build-deploy-pack.ps1,docs/RELEASE_GATE_ENFORCEMENT_V2_TR.md | 2026-03-13 11:30:00 | 2026-03-13 11:30:00 | Release hatti PASS zorunlulugu sertlestirme ve dokuman baglama |
| TASK-013 | codex-b | agent/codex-b/task-013 | active | docs/tasks/TASK-013.md,local-rebuild/apps/orkestram/resources/views/portal/**,local-rebuild/apps/izmirorkestra/resources/views/portal/**,docs/HESABIM_OWNER_HIBRIT_UI_POLISH_TR.md | 2026-03-13 11:45:00 | 2026-03-13 11:45:00 | Hesabim/Owner hibrit akis Faz 2 UI polish ve mobil kirilim temizligi |
