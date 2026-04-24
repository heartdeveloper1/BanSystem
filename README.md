# BanSystem
Plugin de banimento para servidores **PocketMine-MP** (API 2.0.0) desenvolvido por **heartdeveloper**.

---

##  Funcionalidades

- Ban permanente por nome e IP
- Ban temporário com duração customizável (segundos, minutos, horas, dias)
- Remoção de banimento
- Bloqueio automático na entrada (por nome ou IP)
- Expiração automática de bans temporários
- Mensagem de kick personalizada com motivo e tempo restante

---

##  Comandos

| Comando | Descrição | Permissão |
|---|---|---|
| `/banir <jogador> <razao>` | Bane permanentemente | `bansystem.ban` |
| `/banirtempo <jogador> <tempo> <razao>` | Bane por tempo determinado | `bansystem.bantemp` |
| `/removerbanimento <jogador>` | Remove o banimento | `bansystem.unban` |
| `/banhelp` | Lista todos os comandos | — |

### Formato de tempo para `/banirtempo`
| Sufixo | Significado | Exemplo |
|---|---|---|
| `s` | Segundos | `30s` |
| `m` | Minutos | `10m` |
| `h` | Horas | `2h` |
| `d` | Dias | `7d` |

---

##  Permissões

| Permissão | Padrão |
|---|---|
| `bansystem.ban` | OP |
| `bansystem.bantemp` | OP |
| `bansystem.unban` | OP |

---

##  Instalação

1. Coloque o arquivo `BanSystem.phar` na pasta `plugins/` do seu servidor
2. Reinicie o servidor
3. O arquivo `bans.yml` será criado automaticamente em `plugin_data/BanSystem/`

---

##  Requisitos

- PocketMine-MP API **2.0.0**
- PHP **7.0+**

---

##  Informações

- **Autor:** Heartdeveloper
- **Versão:** 1.0.0
- **Discord:** Heartdeveloper

