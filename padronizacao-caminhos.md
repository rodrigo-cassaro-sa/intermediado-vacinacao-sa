# Padronização de Caminhos da Biblioteca

## Objetivo

Garantir que todos os arquivos apontem para os mesmos nomes de pasta.

A estrutura oficial atual é:

```txt
docs/
prompts/
protocols/
skills/
speciality/
```

---

# 1. Mapa de substituição

Use este mapa para corrigir referências antigas.

| Caminho antigo | Caminho oficial |
|---|---|
| `doc/` | `docs/` |
| `/doc` | `/docs` |
| `protocolos/` | `protocols/` |
| `/protocolos` | `/protocols` |
| `especialistas/` | `speciality/` |
| `/especialistas` | `/speciality` |
| `specialists/` | `speciality/` |
| `/specialists` | `/speciality` |

---

# 2. Termos que podem continuar em português

Os nomes dos arquivos podem continuar em português:

```txt
especialista-banco-dados.md
protocolo-criacao-projeto-zero.md
skill-banco-dados.md
```

A pasta é que deve seguir o padrão oficial:

```txt
speciality/especialista-banco-dados.md
protocols/protocolo-criacao-projeto-zero.md
skills/skill-mysql.md
```

---

# 3. Regra para prompts

Todo prompt deve pedir leitura usando caminhos oficiais:

```txt
orquestrador.md
controle-projeto.md
docs/README.md
docs/00-controle-consistencia-projeto.md
protocols/[protocolo].md
speciality/[especialista].md
skills/[skill].md
```

---

# 4. Regra para orquestrador

O orquestrador deve mencionar:

```txt
docs/
prompts/
protocols/
skills/
speciality/
```

Não deve mencionar:

```txt
doc/
protocolos/
especialistas/
```

---

# 5. Checklist de padronização

```md
- [ ] README raiz usa caminhos oficiais.
- [ ] orquestrador.md usa caminhos oficiais.
- [ ] controle-projeto.md usa caminhos oficiais.
- [ ] docs/README.md usa caminhos oficiais.
- [ ] prompts usam caminhos oficiais.
- [ ] protocols usam caminhos oficiais.
- [ ] speciality usa caminhos oficiais.
- [ ] skills README usa caminhos oficiais.
```
