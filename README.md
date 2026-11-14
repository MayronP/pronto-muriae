# Muriaé Pronto — Sistema de Pronto Atendimento Hospitalar

Sistema web para organizar o pronto atendimento, da chegada do paciente à alta, pela gravidade (classificação de risco por cores), desenvolvido em **CodeIgniter 4**.

## Sobre o trabalho

- **Curso:** Análise e Desenvolvimento de Sistemas — Faculdade Santa Marcelina, Muriaé/MG
- **Período:** 6º
- **Disciplinas:** Extensão Universitária VI e Frameworks II
- **Professor orientador:** Aldecir de Almeida Fonseca
- **Ano letivo:** 2026
- **Integrantes:** Davi Maia, Iago Azevedo Lacerda, Italo Andrade de Lacerda Souza e Mayron Silvestre Souza de Paula

## Funcionalidades

- **Admissão:** cadastro de pacientes, inclusive não identificados (nome provisório e características para reconhecimento), e registro de acompanhantes.
- **Triagem:** sinais vitais com cálculo do NEWS2 e classificação de risco em cinco cores, cada uma com seu tempo-alvo.
- **Fila do médico:** ordenada pela gravidade, com aviso de quem passou do tempo-alvo; painel da sala de espera (TV) com primeiro nome e inicial.
- **Atendimento:** evolução médica com CID-10, exames, anexos, reclassificação, observação e desfecho (alta, alta a pedido, internação, transferência, óbito ou evasão).
- **Registros de enfermagem:** sinais vitais seriados (com NEWS2) e evolução de enfermagem.
- **Histórico do paciente:** atendimentos anteriores, com o prontuário de cada um.
- **Medicação:** prescrição com alerta de alergia e de duplicidade, aprazamento automático das doses e checagem dose a dose.
- **Protocolos gerenciados:** dor torácica, AVC e sepse, com prazo por etapa.
- **Rotinas:** passagem de plantão (SBAR) com confirmação de quem assume, notificações compulsórias com prazo de envio e documentos (receita, atestado, encaminhamento e declaração de comparecimento).
- **Gestão:** painel com a situação atual do pronto atendimento, relatórios por período e auditoria das ações.

Acesso por perfil: **Administrador**, **Enfermagem** e **Médico**.

## Tecnologias

- PHP 8.2+ (extensões `intl`, `mbstring` e `mysqli`)
- [CodeIgniter 4](https://codeigniter.com)
- MySQL 8
- Bootstrap 5.3 e Font Awesome 4.7 (incluídos em `public/assets`)

## Como executar

Confira a versão com `php -v` e utilize PHP 8.2 ou superior, inclusive na execução do Composer. No Windows, ajuste o PATH do terminal ou informe o caminho completo do executável compatível.

1. Instale as dependências:
   ```
   composer install
   ```
2. Copie `.env.example` para `.env` e ajuste os valores conforme seu ambiente:
   ```
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = pronto_atendimento
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```
3. Crie o banco vazio (`utf8mb4`):
   ```
   CREATE DATABASE pronto_atendimento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Crie as tabelas e os dados iniciais (perfis, usuários, classificações de risco, protocolos e CID-10):
   ```
   php spark migrate
   php spark db:seed DatabaseSeeder
   ```
5. Suba o servidor embutido do CodeIgniter e acesse `http://localhost:8080`:
   ```
   php spark serve
   ```

> O `index.php` fica dentro da pasta `public`. No WAMP, aponte o virtual host para essa pasta (não para a raiz do projeto) e ajuste a `app.baseURL` no `.env`.

### Usuários iniciais

| Login | Perfil | Senha inicial |
| --- | --- | --- |
| `admin` | Administrador | `pronto@123` |
| `enfermagem` | Enfermagem | `pronto@123` |
| `medico` | Médico | `pronto@123` |

Altere as senhas após o primeiro acesso (menu do Administrador › Usuários).

## Como testar

Com o servidor rodando, siga o fluxo com um mesmo paciente:

1. Entre como `enfermagem`. Em **Pacientes › Novo paciente**, cadastre um adulto com alergia a dipirona; o sistema segue para a admissão e, depois, para a triagem. Registre os sinais vitais e a cor: a mensagem mostra o NEWS2.
2. Entre como `medico`. Na **Fila** (ordem por cor e chegada), use **Chamar no painel** no menu ⋯ da linha (o nome aparece em `/painel`, que não exige login) e clique em **Iniciar**. Registre a evolução com CID-10, solicite um exame e prescreva dipirona: aparece o alerta de alergia, que exige confirmação.
3. Entre como `enfermagem`. Na **Fila**, abra **Medicamentos** no menu ⋯ da linha e cheque a dose; depois, em **Passagem de plantão**, registre a passagem do paciente.
4. Entre como `medico`, abra o atendimento (**Continuar** na Fila) e use **Encerrar atendimento** com um desfecho. Depois do encerramento, o atendimento não aceita checagem de doses nem passagem de plantão.
5. Entre como `admin`: confira o atendimento em **Dashboard** e **Relatórios** e as ações registradas em **Auditoria**.
6. Permissões: como `enfermagem`, abra `/admin/usuarios`; o sistema volta ao dashboard com "Acesso não autorizado para o seu perfil."

Execute `php vendor/bin/phpunit --no-coverage` para rodar a suíte automatizada, que inclui testes de validação de CPF. As verificações de software devem usar dados fictícios e não constituem validação clínica.

## Limites do protótipo

- Não emite receita de medicamentos controlados: a integração com o SNCR da Anvisa exige assinatura ICP-Brasil, médico com CRM ativo, conta gov.br e domínio `.br` com CNPJ.
- A classificação de risco usa as cores e os tempos-alvo do Sistema Manchester apenas como referência: não implementa os fluxogramas e discriminadores (a cor é escolhida pelo profissional), e o NEWS2 é um alerta que não muda a cor nem a ordem da fila.
- Não foi validado em plantão real; os dados de uso devem ser fictícios.

## Licença

Código do CodeIgniter 4 sob a licença MIT (ver `LICENSE`).


## Documentação e manutenção

- [Modelo ER e dicionário](docs/modelo-er.md).
- [Instalação, migration e produção](docs/instalacao-producao.md).
- Manual do usuário e relatório extensionista: documentos mantidos pela equipe.

Antes de atualizar uma instalação existente, faça backup e teste sua restauração em uma cópia. Execute as migrations para aplicar a evolução do esquema. O campo `tempo_alvo_inicio` preserva a meta vigente no início do atendimento; registros antigos sem essa informação ficam fora do percentual histórico.

Logout usa POST com CSRF. CPF informado exige dígitos verificadores válidos. As rotas de escrita autenticadas usam um lock por banco MySQL para evitar corridas entre workers; isso limita a vazão e não atende a arquitetura com múltiplos servidores de banco. A trilha de auditoria tem aviso e fallback em caso de falha, sem garantia de atomicidade integral.

NEWS2 usa apenas a escala 1 de saturação; escala 2 não está implementada. A observação exibe aviso visual perto/acima de 24h ao consultar a tela. Dashboard e fila não têm polling; o painel TV recarrega a cada 10s.
