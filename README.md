# Act Coffee

Act Coffee e uma aplicacao Laravel para organizar automaticamente a escala de lavagem da cafeteira no trabalho.

## Descricao

O sistema substitui uma planilha manual por uma escala publica e uma area administrativa. Qualquer pessoa pode ver quem e o responsavel do dia, os proximos responsaveis e o historico recente. O administrador gerencia funcionarios, ferias, feriados personalizados e a escala do dia.

## Problema resolvido

A planilha manual nao pula fins de semana e feriados automaticamente, nao lida bem com ferias, desligamentos ou entrada de novos funcionarios, e exige manutencao recorrente. O Act Coffee centraliza essas regras e mantem a fila consistente.

## Publico-alvo

Equipes internas que compartilham uma cafeteira e precisam dividir a responsabilidade de limpeza de forma simples, justa e previsivel.

## Funcionalidades principais

- Visualizacao publica do responsavel de hoje.
- Lista publica dos proximos responsaveis.
- Historico dos ultimos 30 dias.
- Login administrativo.
- CRUD de funcionarios.
- Inativacao de funcionarios sem apagar historico.
- CRUD de ferias.
- CRUD de feriados personalizados.
- Feriados nacionais fixos do Brasil.
- Feriados moveis calculados a partir da Pascoa pelo algoritmo de Meeus.
- Feriados locais de Guarapuava em 09/12.
- Botao para marcar a lavagem como concluida.
- Botao para trocar o responsavel do dia pelo proximo funcionario disponivel.
- Testes automatizados para as principais regras de negocio.

## Print da tela mais interessante

![Tela publica do Act Coffee](public/images/coffee-station.png)

## Requisitos

- PHP 8.2 ou superior.
- Composer.
- Node.js 22 ou superior.
- npm.
- SQLite habilitado no PHP.

## Instalacao e execucao local

```bash
composer install
npm install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

Acesse:

```text
http://localhost:8000
```

## SQLite e .env.example

O projeto usa SQLite por padrao. O arquivo `.env.example` ja vem com:

```env
APP_NAME="Act Coffee"
APP_TIMEZONE=America/Sao_Paulo
DB_CONNECTION=sqlite
```

Se quiser usar um caminho absoluto para o banco, defina `DB_DATABASE` no `.env`. Caso contrario, o Laravel usa `database/database.sqlite`.

## Credenciais de teste

O seeder cria um usuario administrativo:

```text
Email: admin@example.com
Senha: password
```

## Modulos da disciplina usados

1. Modulo 4 - Rotas, MVC, Controllers, Actions e Services
2. Modulo 5 - Blade e Server Side Rendering
3. Modulo 6 - TailwindCSS
4. Modulo 7 - Validacao de dados
5. Modulo 8 - Autenticacao
6. Modulo 9 - Migrations, Models e Eloquent ORM
7. Modulo 10 - Testes automatizados, factories e seeders

## Onde cada modulo foi aplicado

Modulo 4 aparece nas rotas em `routes/web.php`, nos controllers publicos/admin e nos services `HolidayService`, `EmployeeQueueService`, `ScheduleGeneratorService` e `HistoryCleanupService`.

Modulo 5 aparece nas views Blade em `resources/views`, separadas entre layout publico, layout administrativo, telas publicas e CRUDs.

Modulo 6 aparece no uso de TailwindCSS via Vite em `resources/css/app.css` e nas classes utilitarias das views.

Modulo 7 aparece nos Form Requests `EmployeeRequest`, `VacationRequest` e `CustomHolidayRequest`.

Modulo 8 aparece no login administrativo com sessoes Laravel, middleware `auth` e rotas protegidas em `/admin`.

Modulo 9 aparece nas migrations, models e relacionamentos de `Employee`, `Vacation`, `CustomHoliday`, `CoffeeDuty` e `User`.

Modulo 10 aparece nos testes em `tests/Feature`, na `EmployeeFactory` e no `DatabaseSeeder`.

## Testes

Rode:

```bash
php artisan test
```

Os testes cobrem:

- Pular sabado e domingo.
- Pular feriado fixo nacional.
- Calcular a Pascoa pelo algoritmo de Meeus.
- Pular feriado movel.
- Pular feriado personalizado.
- Ignorar funcionario inativo.
- Inserir novo funcionario no final da fila.
- Pular funcionario em ferias apenas quando sua vez cai no periodo.
- Manter a ordem da fila apos ferias.
- Trocar responsavel do dia pelo proximo disponivel.
- Marcar lavagem como concluida.
- Limpar historico com mais de 30 dias.
- Proteger a area administrativa com login.

## CI

O workflow `.github/workflows/tests.yml` instala dependencias PHP e JS, prepara SQLite, gera a chave da aplicacao, roda migrations, compila assets e executa `php artisan test`.

## Melhorias futuras

- Enviar lembretes por email.
- Integrar com Google Calendar.
- Exportar escala em PDF.
- Permitir multiplas equipes.
- Registrar estatisticas de participacao.
- Ajustar manualmente a fila pelo painel administrativo.
- Enviar notificacoes por Slack, Teams ou WhatsApp.
