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
| TASK-AGENT-BOOTSTRAP | codex-a | agent/codex-a/task-agent-bootstrap | closed | - | 2026-03-13 08:41:35 | 2026-03-13 23:10:00 | Bootstrap tamamlandi, lock kapatildi |
| TASK-001 | codex | agent/codex/task-001 | closed | docs/NEXT_TASK.md,docs/WORKLOG.md,docs/PROJECT_STATUS_TR.md,docs/REPO_DISCIPLINE_TR.md,docs/MULTI_AGENT_RULES_TR.md,docs/TASK_LOCKS.md,docs/tasks/_TEMPLATE.md,scripts/start-task.ps1 | 2026-03-13 08:41:51 | 2026-03-13 23:30:00 | Multi-agent disiplin seti finalize edildi |
| TASK-002 | codex | agent/codex/task-001 | closed | docs/NEXT_TASK.md,docs/WORKLOG.md,docs/TASK_LOCKS.md,docs/tasks/TASK-002.md,docs/UI_LABEL_AUDIT_TR.md,local-rebuild/apps/orkestram/resources/views/portal/**,local-rebuild/apps/izmirorkestra/resources/views/portal/** | 2026-03-13 23:30:00 | 2026-03-13 23:55:00 | UI label parity taramasi tamamlandi, pre-pr PASS |
| TASK-003 | codex-a | agent/codex-a/task-003 | closed | docs/tasks/TASK-003.md,docs/TEKLIF_FORMU_FAZ2_PLAN_TR.md | 2026-03-13 09:45:01 | 2026-03-13 23:58:00 | Koordinator talebiyle lock kapatildi, yeni gorev TASK-008'e gecildi |
| TASK-004 | codex | agent/codex/task-001 | closed | docs/tasks/TASK-004.md,docs/PERFORMANCE_BASELINE_REPORT_TR.md,scripts/perf-baseline.ps1 | 2026-03-13 09:45:01 | 2026-03-13 10:12:00 | Performans baseline olcum ve rapor iskeleti tamamlandi |
| TASK-005 | codex | agent/codex/task-001 | closed | docs/tasks/TASK-005.md,docs/PERFORMANCE_BASELINE_ACTION_PLAN_TR.md | 2026-03-13 10:15:00 | 2026-03-13 10:35:00 | Baseline action plan tamamlandi, pre-pr PASS |
| TASK-006 | codex | agent/codex/task-001 | closed | docs/tasks/TASK-006.md,docs/SERVICE_AREA_FALLBACK_DARALTMA_PLANI_TR.md | 2026-03-13 10:09:02 | 2026-03-13 10:12:00 | Lock serbest birakildi, gorev koordinasyona gore devredildi |
| TASK-007 | codex | agent/codex/task-001 | closed | docs/tasks/TASK-007.md,docs/BRANCH_PROTECTION_REQUIRED_CHECKS_TR.md | 2026-03-13 10:20:00 | 2026-03-13 10:28:00 | Branch protection required check ve merge gate plani tamamlandi |
| TASK-008 | codex-a | agent/codex-a/task-008 | closed | docs/tasks/TASK-008.md | 2026-03-13 23:58:00 | 2026-03-14 00:12:00 | Gorev kapatildi, pre-pr PASS |
| TASK-009 | codex-b | agent/codex-b/task-009 | closed | docs/tasks/TASK-009.md,docs/SERVICE_AREA_FALLBACK_DARALTMA_PLANI_TR.md | 2026-03-13 10:40:00 | 2026-03-13 11:45:00 | Plan checkliste cevrildi, pre-pr PASS, push tamamlandi |
| TASK-010 | codex | agent/codex/task-001 | closed | docs/tasks/TASK-010.md,docs/NEXT_TASK.md,docs/WORKLOG.md,docs/TASK_LOCKS.md | 2026-03-13 10:40:00 | 2026-03-13 11:20:00 | Koordinator sync turu tamamlandi, aktif gorevler netlestirildi |
| TASK-011 | codex-a | agent/codex-a/task-011 | closed | docs/tasks/TASK-011.md,scripts/dev-up.ps1,scripts/validate.ps1,docs/RUNTIME_PERMISSION_HARDENING_TR.md | 2026-03-13 11:30:00 | 2026-03-13 12:24:00 | Runtime izin sertlestirme tamamlandi, pre-pr quick PASS |
| TASK-012 | codex | agent/codex/task-001 | active | docs/tasks/TASK-012.md,scripts/release.ps1,scripts/build-deploy-pack.ps1,docs/RELEASE_GATE_ENFORCEMENT_V2_TR.md | 2026-03-13 11:30:00 | 2026-03-13 11:30:00 | Release hatti PASS zorunlulugu sertlestirme ve dokuman baglama |
| TASK-013 | codex-b | agent/codex-b/task-013 | active | docs/tasks/TASK-013.md,local-rebuild/apps/orkestram/resources/views/portal/**,local-rebuild/apps/izmirorkestra/resources/views/portal/**,docs/HESABIM_OWNER_HIBRIT_UI_POLISH_TR.md | 2026-03-13 11:45:00 | 2026-03-13 11:45:00 | Hesabim/Owner hibrit akis Faz 2 UI polish ve mobil kirilim temizligi |
