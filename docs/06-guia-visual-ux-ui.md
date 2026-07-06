# Guia Visual, UX e UI

## Objetivo

Definir identidade visual, conforto de uso, estados de tela e padrões de interface.

---

# 1. Identidade visual

```txt
Nome/marca:
Estilo:
Tom:
Referências:
```

---

# 2. Paleta de cores

| Uso | Cor | Observação |
|---|---|---|
| Primária |  |  |
| Secundária |  |  |
| Fundo |  |  |
| Texto |  |  |
| Erro |  |  |
| Sucesso |  |  |

---

# 3. Tipografia

| Uso | Fonte/Tamanho | Observação |
|---|---|---|
| Título |  |  |
| Texto |  |  |
| Botão |  |  |

---

# 4. Componentes

| Componente | Padrão | Estados |
|---|---|---|
| Botão |  | normal/loading/desabilitado |
| Campo |  | normal/foco/erro |
| Card |  |  |
| Modal |  |  |
| Tabela |  |  |

---

# 5. Estados obrigatórios

Toda tela com dados deve prever:

```txt
carregando
vazio
erro
sucesso
sem permissão
offline, quando aplicável
```

---

# 6. Motion e feedback visual

- Ação do usuário deve ter resposta visual rápida.
- Loading deve ser local quando possível.
- Skeleton deve ser usado em carregamento de listas/cards.
- Optimistic UI não deve confirmar ação crítica sem backend.

---

# 7. Responsividade

| Dispositivo | Estratégia |
|---|---|
| Desktop |  |
| Tablet |  |
| Mobile |  |

---

# 8. Acessibilidade

```md
- [ ] Contraste adequado.
- [ ] Foco visível.
- [ ] Não depender só de cor.
- [ ] Labels em campos.
- [ ] Navegação compreensível.
```
