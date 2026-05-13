# Catálogo Trisoft

Sistema de catálogo de produtos com carrinho de orçamentos (MVP).

- **Stack:** PHP 8.2+, MySQL 8, PDO, Tailwind, Alpine.js
- **URL temporária:** https://trisoft.com.br/catalogo2
- **Repo:** https://github.com/gholive84/trisoft-catalogo

## Setup local

```bash
composer install
cp .env.example .env
# preencher .env com credenciais reais
php database/migrate.php
php database/seed.php             # admin + settings padrão
php database/demo_seed.php        # (opcional) categorias e produtos de exemplo
```

Para servir localmente:

```bash
php -S localhost:8000 -t public
```

Acesse: http://localhost:8000

## Deploy (SiteGround)

```bash
ssh -p 18765 u2550-7wftgcpgoimd@gtxm1030.siteground.biz
cd catalogo2
git pull
composer install --no-dev --optimize-autoloader
php database/migrate.php
```

## Estrutura

- `public/` — document root (index.php + assets + uploads)
- `src/` — código PHP organizado por camadas (Core, Models, Repositories, Services, Controllers, Middleware)
- `templates/` — views PHP nativas
- `database/migrations/` — SQL numerado
- `scripts/` — utilitários (migração WP, crons)
- `storage/` — logs e cache (fora do public)

## Convenções

- PSR-4 autoload (`App\` → `src/`), PSR-12 code style
- PDO + prepared statements em todo acesso ao banco
- CSRF token em todo POST/PUT/DELETE
- `password_hash(PASSWORD_BCRYPT)` para senhas
- Commits em PT-BR: `feat:` / `fix:` / `chore:` / `docs:` / `refactor:`
- Sem branches no MVP — main direto
