# Importação de Catálogos a partir de PDF

Documentação do processo usado para importar o **catálogo de Baffles** e reaproveitável
para os próximos catálogos da Trisoft (Painéis Acústicos, Difusores, Revestimentos,
Cloud, Fachadas, etc.) que seguem o **mesmo padrão visual e estrutural**.

---

## 📋 Padrão esperado do PDF

Os PDFs da Trisoft seguem o template:

```
┌─────────────────────────────────┐
│  Páginas 1-6 (capa, missão,     │  ← intro, NÃO são produtos
│  história, sumário)             │
├─────────────────────────────────┤
│  Página N (ímpar)    │ HERO     │  ← foto grande do produto em ambiente
│  ┌─────────┐         │          │      Título: "BAFFLE LINHA SHAPE"
│  │         │         │          │      Subtitle: "SOLID", "PRINTED", etc.
│  │  foto   │         │          │
│  │         │         │          │
│  └─────────┘                    │
├─────────────────────────────────┤
│  Página N+1 (par)    │ SPECS    │  ← tabela com SKUs
│  Code  Thickness  A  B  ...     │
│  XX-YY-NN-NNNN  50  200 1200    │      Mesmo título no topo
│  ...                            │
└─────────────────────────────────┘
```

**Convenções confirmadas no catálogo de Baffles:**
- Cada produto ocupa **2 páginas consecutivas** (hero + specs).
- A primeira linha não-vazia da página é o título: `BAFFLE XXX YYY` (UPPERCASE).
- A segunda linha é o subtítulo: `SOLID`, `SOLID - HIGH RELIEF`, `DECOR PRINTED`, etc.
- Páginas de hero **não** contêm as palavras "Code" nem "Thickness" (usado para distinguir).
- SKUs seguem o padrão `B[A-Z]+-[A-Z]+-NN-NNNN` (ex.: `BC-STR-50-0001`).
- Tabela de specs tem colunas: `Code`, `Thickness (mm)`, `A (mm)`, `B (mm)`,
  `Pieces per box`, `Coverage area (m²)`, `PET Bottles`.
- Bullet ` • ` ou `–` entre tokens do subtitle (ex.: `SOLID • HIGH RELIEF`).

---

## 🛠 Pré-requisitos

### Local (Windows, no dev)

```bash
which pdftotext   # → /mingw64/bin/pdftotext (vem com Git for Windows)
```

Se faltar: instale Git for Windows ou baixe `poppler-utils` standalone.

### Servidor SiteGround

Já tem instalado (confirmado em `gtxm1030.siteground.biz`):
- `magick` (ImageMagick 7.1.1)
- `gs` (Ghostscript) — usado por ImageMagick para renderizar PDF
- `php` 8.2.31

**NÃO tem** `pdftotext` no SiteGround. Por isso a extração de texto é feita
**localmente** e o `.txt` resultante é enviado por SCP.

---

## 🚀 Processo passo-a-passo (catálogo novo)

### 1. Preparar o PDF localmente

```bash
# Salve o PDF em catalogos/ (ignorado pelo git — ver .gitignore)
mkdir -p catalogos/
mv ~/Downloads/catálogo_paineis_TRISOFT.pdf catalogos/

# Extrai o texto preservando layout das tabelas + UTF-8.
# A flag -layout é essencial: mantém colunas alinhadas para o regex de SKU.
# Sem -layout, cada coluna fica em linha separada.
pdftotext -layout -enc UTF-8 \
    "catalogos/catálogo_paineis_TRISOFT.pdf" \
    /tmp/paineis_layout.txt
```

### 2. Inspecionar o padrão do novo PDF

Antes de rodar o import, **verifique** se o novo PDF segue o mesmo padrão dos baffles:

```bash
# Quantas páginas
awk 'BEGIN{RS="\f"} END{print "Páginas:", NR}' /tmp/paineis_layout.txt

# Procurar pelo título principal (PAINEL, BAFFLE, DIFUSOR, etc.)
awk 'BEGIN{RS="\f"} {if (match($0, /^[A-Z]+ [A-Z]+/)) print NR": "substr($0, RSTART, 60)}' \
    /tmp/paineis_layout.txt | head -20

# Padrões de SKU
grep -oE "[A-Z]{1,3}-[A-Z]+-[0-9]+-[0-9]+" /tmp/paineis_layout.txt | sort -u | head -20

# Total de SKUs únicos
grep -oE "[A-Z]{1,3}-[A-Z]+-[0-9]+-[0-9]+" /tmp/paineis_layout.txt | sort -u | wc -l
```

Se o padrão difere (ex.: 3 páginas por produto, formato de SKU diferente), ajuste o
script (próximo passo). Senão, prossiga.

### 3. Adaptar o script de import (se necessário)

O script `scripts/import_baffles_pdf.php` é o template. Para um novo catálogo,
**duplique** e ajuste:

```bash
cp scripts/import_baffles_pdf.php scripts/import_paineis_pdf.php
```

Pontos a ajustar:

| Variável | Onde | Para baffles | Ajuste típico p/ novo catálogo |
|----------|------|--------------|-------------------------------|
| Categoria raiz | `ensureCategory($pdo, 'baffles', 'Baffles')` | `baffles` | `paineis-acusticos`, `difusores`, etc. |
| Sub-categorias | `$lineCategories`, `$shapeCategories` | Classic/Ness/Form + shapes | Linhas do novo produto |
| Regex do título | `/^BAFFLE\s+([A-Z]+)\s+([A-Z]+(?:\s+[A-Z]+)*)$/u` | `BAFFLE` | Trocar por `PAINEL`, `DIFUSOR`, etc. |
| Regex do SKU | `B[A-Z]+-[A-Z]+-\d+-\d+` | `BC-`, `BF-`, `BN-` | Verificar prefixos do novo PDF |
| Padrão hero/specs | 2 páginas (hero ímpar, specs par) | OK | Se for 3 pgs/produto, ajustar `$pages[$idx+1]` |
| `--reset` filter | `DELETE FROM products WHERE sku REGEXP '^B[A-Z]-'` | Apaga só baffles | Limitar pelo prefixo correto |

### 4. Upload do PDF e texto para o servidor

```bash
SCP=/c/Windows/System32/OpenSSH/scp.exe
KEY=~/.ssh/trisoft_siteground_open

# Upload do PDF (necessário para extração de imagens)
$SCP -i $KEY -P 18765 \
    "catalogos/catálogo_paineis_TRISOFT.pdf" \
    u2550-7wftgcpgoimd@gtxm1030.siteground.biz:/tmp/paineis.pdf

# Upload do texto extraído (usado pelo parser)
$SCP -i $KEY -P 18765 \
    /tmp/paineis_layout.txt \
    u2550-7wftgcpgoimd@gtxm1030.siteground.biz:/tmp/paineis_layout.txt
```

### 5. Rodar o import dos produtos

```bash
SSH=/c/Windows/System32/OpenSSH/ssh.exe
$SSH -i $KEY -p 18765 u2550-7wftgcpgoimd@gtxm1030.siteground.biz \
    'cd ~/www/trisoft.com.br/public_html/catalogo2 &&
     git pull --quiet &&
     php scripts/import_paineis_pdf.php /tmp/paineis_layout.txt --reset'
```

Saída esperada:
```
PDF tem 87 páginas.
Encontrados 32 produtos únicos.

+ Inserido: PAINEL ACÚSTICO LINUS - SOLID (page 7, 6 specs)
...
✅ Inseridos: 32 | Atualizados: 0
```

### 6. Rodar a extração de imagens

```bash
$SSH -i $KEY -p 18765 u2550-7wftgcpgoimd@gtxm1030.siteground.biz \
    'cd ~/www/trisoft.com.br/public_html/catalogo2 &&
     php scripts/extract_pdf_images.php /tmp/paineis.pdf'
```

O script `extract_pdf_images.php` é genérico — não precisa duplicar. Lê entradas
`_baffles_hero_page_*` em settings (TODO: renomear para `_hero_page_*` quando
criar o segundo catálogo).

**Gera DUAS imagens por produto:**

1. **Hero** — `<slug>.jpg` em 1920×1080, JPG q85 (~200–400 KB), renderizado da
   página ímpar do PDF (página com foto grande do produto em ambiente).
   Salvo em `products.hero_image_path` + `product_images` (is_main=1).

2. **Sugestões de Modulação** — `<slug>-modulation.png`, faixa horizontal
   recortada da página de specs (hero_page + 1). Mostra os ícones wireframe das
   variações de modulação que aparecem no PDF acima da tabela. Salvo em
   `products.modulation_image_path`. Usado pelo template `public/product.php`
   na seção "Sugestões de Modulação".

**Flags úteis:**

| Flag | Descrição |
|------|-----------|
| `--density=N` | DPI de render (default 250). Maior = imagem maior + mais qualidade. |
| `--quality=N` | JPEG quality (default 85). |
| `--limit=N` | Processa só N produtos (debug). |
| `--force` | Re-renderiza mesmo que o arquivo já exista. |
| `--skip-hero` | Pula heroes (só gera modulações). |
| `--skip-modulation` | Pula modulações (só gera heroes). |
| `--mod-crop="WxH+X+Y"` | Define a região de crop da modulação em % da página. Default: `90%x12%+5%+28%` (faixa horizontal central na altura de "Modulation suggestions" do layout Trisoft). |

**Idempotência:** o script pula arquivos que já existem. Use `--force` para
re-renderizar tudo (útil se mudar o crop ou o subtitle/slug do produto).

**Ajuste do crop de modulação:** se o novo PDF tem layout diferente, ajuste o
`--mod-crop`. Para descobrir o valor certo:
```bash
# Renderiza só uma página completa pra inspecionar visualmente
magick /tmp/paineis.pdf[7] -density 200 /tmp/page8.png
# Abre /tmp/page8.png, mede as coordenadas da faixa de modulação (% da página)
# e passa --mod-crop=X%xY%+Z%+W%
```

### 7. Validar via navegador

```
https://trisoft.com.br/catalogo2/?_nc=$(date +%s)
https://trisoft.com.br/catalogo2/categoria/paineis-acusticos?_nc=1
```

Cache-bust (`?_nc=...`) é necessário enquanto o cache do nginx SiteGround estiver
ativo. Para invalidar permanentemente, ir em Site Tools → Speed → Caching →
Flush Cache.

---

## 🔧 Scripts envolvidos

| Arquivo | Função |
|---------|--------|
| [`scripts/import_baffles_pdf.php`](../scripts/import_baffles_pdf.php) | Parser do texto extraído + cria produtos + tabela `specifications` JSON. Template para outros catálogos. |
| [`scripts/extract_pdf_images.php`](../scripts/extract_pdf_images.php) | Renderiza heroes (JPG 1920×1080) + modulações (PNG crop da página de specs) usando `magick`. |
| [`database/migrations/015_add_specifications_to_products.sql`](../database/migrations/015_add_specifications_to_products.sql) | Adiciona `specifications JSON`, `subtitle`, `hero_image_path` em `products`. |
| [`database/migrations/016_add_modulation_image_to_products.sql`](../database/migrations/016_add_modulation_image_to_products.sql) | Adiciona `modulation_image_path` em `products` para a faixa de modulação extraída do PDF. |

---

## 🧠 Decisões técnicas chave

1. **`pdftotext -layout` é obrigatório.** Sem `-layout`, cada coluna da tabela
   de specs fica em linha separada e o regex perde o contexto da linha de SKU.

2. **Separador `\f` (form-feed) entre páginas.** O `pdftotext` insere `\f` entre
   páginas naturalmente. Usamos `explode("\f", $raw)` para iterar por página.

3. **Detecção heurística de hero vs specs.** Páginas de hero têm título +
   subtitle nas primeiras linhas e **não** contêm "Code"/"Thickness". Páginas
   de specs têm a tabela completa.

4. **Idempotência via slug.** O parser gera slug a partir do nome do produto.
   Re-execuções atualizam pelo slug em vez de duplicar.

5. **Imagens 1920×1080 q85.** Cobre bem hero full-bleed (16:9) e ainda fica
   leve (~200-400KB). Cropping centralizado preserva o foco do produto.

6. **ImageMagick + Ghostscript do SiteGround.** Disponíveis nativamente. Sem
   necessidade de instalar nada.

7. **Idempotência de imagens.** Script pula arquivos existentes; `--force`
   re-renderiza tudo (útil quando muda o subtitle/slug).

---

## 🚨 Limitações conhecidas

- **Variações com layout não-padrão são puladas.** Ex.: bass traps gigantes com
  pacote diferente, kits combinados que não têm tabela tradicional. Esses
  precisam cadastro manual via admin (Sprint 4) ou ajuste fino do parser.

- **Apenas 1 imagem hero por produto.** O PDF pode ter mais imagens (ex.:
  variação de cor, detalhe técnico). Para múltiplas, usar admin ou extender
  `extract_pdf_images.php` para também extrair pages adjacentes.

- **Sem extração das amostras de cor** ("color swatches" no PDF — pequenas
  amostras retangulares de tecido). Esses ficam só como referência no PDF
  original.

- **Subtitle pode misturar bullet `•` com hífen** em algumas páginas. O parser
  normaliza para hífen mas saídas como `SOLID - HIGH RELIEF` vs
  `SOLID • HIGH RELIEF` podem aparecer.

---

## 📊 Resultado do catálogo Baffles (referência)

- **PDF de origem:** 146 páginas, ~35 MB
- **Produtos identificados (heroes):** 70
- **Produtos importados com specs:** 57
- **Imagens renderizadas:** 45 (1920×1080 JPG)
- **Total de SKUs nas tabelas de specifications:** ~700
- **Tempo total:** ~3 minutos (parser + extração)
- **Espaço em uploads:** ~12 MB (45 × ~270KB)

---

## 🔮 Próximas evoluções sugeridas

1. **Generalizar scripts:** parametrizar com flags `--root-category`,
   `--title-prefix`, `--sku-regex` em vez de duplicar para cada catálogo.
2. **Extrair múltiplas imagens por produto** (color swatches, detalhes).
3. **Extrair imagens das tabelas de SKU** (cada SKU pode ter foto própria
   em alguns PDFs).
4. **Migrar settings._baffles_hero_page_*** para um campo dedicado na tabela
   `products` (ex.: `_pdf_source` JSON com `{pdf, page, line}`) para preservar
   rastreabilidade da origem.
