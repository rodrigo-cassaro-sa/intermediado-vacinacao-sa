# Banco de Dados — Migrations e Seeds

Base de modelagem: [docs/08-modelagem-banco-dados.md](../docs/08-modelagem-banco-dados.md).
Segurança/LGPD: [docs/10-seguranca-lgpd-auditoria.md](../docs/10-seguranca-lgpd-auditoria.md).

## Convenções

- Engine **InnoDB**, charset **utf8mb4** (`utf8mb4_unicode_ci`).
- IDs `BIGINT UNSIGNED AUTO_INCREMENT`.
- Campos de negócio em **português**; termos técnicos (`tenant_id`, `request_id`) em inglês.
- Toda tabela de negócio tem `tenant_id`; identidade/catálogo global sem tenant (`paciente`, `vacina`, `clinica_credenciada`).
- `aplicacao` é **imutável** (sem `atualizado_em`); correção = novo registro (RN-010).
- Controle de execução na tabela `schema_migracao` (cada arquivo se auto-registra ao final).

## Ordem de aplicação (respeita as foreign keys)

```txt
000_criar_controle_migracoes.sql
001_criar_cliente_b2b_usuario.sql
002_criar_paciente_vacina.sql
003_criar_campanha_campanha_vacina.sql
004_criar_elegivel_importacao.sql
005_criar_clinica_credencial_api.sql
006_criar_aplicacao.sql
007_criar_consentimento_log_auditoria.sql
008_criar_view_tabela_verdade.sql
seeds/seeds_vacinas_perfis.sql   (por último; ajuste o senha_hash antes)
```

## Como aplicar (exemplo)

```bash
# Ajuste host/usuário/banco conforme o ambiente (dev/homolog/produção).
for f in database/migrations/0*.sql; do
  echo "Aplicando $f"
  mysql -h HOST -u USUARIO -p BANCO < "$f"
done
mysql -h HOST -u USUARIO -p BANCO < database/seeds/seeds_vacinas_perfis.sql
```

## Cuidados

- **Não aplicar em produção sem backup** (regra global do orquestrador).
- Antes do seed, gerar o hash real da senha do admin: `php -r "echo password_hash('SUA_SENHA', PASSWORD_DEFAULT);"`.
- MySQL apenas em rede interna; phpMyAdmin protegido (docs/10, docs/13).

## Status

> Os arquivos SQL foram escritos conforme o doc 08, mas **ainda não foram executados/validados
> contra um MySQL** (nenhum ambiente provisionado). Validar em um banco de desenvolvimento antes
> de considerar concluído. Ver checklist no `controle-projeto.md`.
