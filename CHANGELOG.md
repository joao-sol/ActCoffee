# Changelog

Todas as alterações relevantes do Act Coffee serão documentadas neste arquivo.

## [1.1.2] - 2026-11-06

### Corrigido

- Troca de um dia que já era pagamento de outra troca: a dívida passa a ficar com quem estava pagando, e não com o dono original do dia.
- Criação indevida de uma troca automática quando o novo devedor cumpre esse pagamento.
- Ajuste na escala de produção: Chiara assume o dia de Hellen em 12/11/2026 (antes estava com Victor).

### Alterado

- Nova coluna `covered_employee_id` em `coffee_duties`, que registra de quem era a vez coberta numa troca em cadeia. Requer `php artisan migrate`.

### Corrigido

- Continuidade da fila após a inativação de um funcionário que ainda aparece como responsável original em uma troca registrada.
- Reinício indevido da escala no primeiro funcionário ativo após a remoção de alguém da fila.

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
