# Changelog

Todas as alterações relevantes do Act Coffee serão documentadas neste arquivo.

## [1.1.0] - 2026-09-15

### Adicionado

- Conclusão automática das lavagens após três dias úteis.
- Comando `php artisan act-coffee:concluir-pendentes` para executar a conclusão automática.
- Possibilidade de registrar trocas após a data da lavagem, enquanto o prazo estiver aberto.
- Dropdown de troca nos dois registros mais recentes e elegíveis do histórico público.

### Alterado

- A rotina de conclusão é executada diariamente às `00:05` pelo agendador do Laravel.
- Fins de semana e feriados não são contabilizados no prazo de três dias úteis.
- A área pública mantém o histórico recente e apresenta os controles de troca de forma compacta.

### Removido

- Botões e rotas de conclusão manual das áreas pública e administrativa.
