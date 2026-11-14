# Instalação e operação

Use PHP 8.2 ou superior, compatível com o arquivo Composer lock, e confira a versão com `php -v`. Configure o servidor web para servir somente a pasta `public`. Instale as dependências pelo Composer, crie um banco vazio em utf8mb4, configure o `.env` e execute `php spark migrate` e `php spark db:seed DatabaseSeeder`.

Para atualizar uma instalação existente: interrompa as escritas, faça backup verificado e teste sua restauração em outro banco. Execute `php spark migrate` usando o PHP correto. A nova migration acrescenta tempo_alvo_inicio nullable e não altera registros antigos. Atendimentos iniciados antes dessa mudança não possuem meta histórica recuperável; os relatórios os excluem do percentual de meta.

O exemplo `.env.production.example` oferece ambiente production, HTTPS forçado, cookie Secure e erros SQL ocultos. Preencha os valores conforme o ambiente de destino. Use credenciais próprias com menor privilégio; não implante as contas demonstrativas. Confirme certificado HTTPS e baseURL, permissões de escrita restritas a writable e headers/cookies no ambiente de destino.

Backup precisa incluir banco e writable/uploads, com acesso restrito e retenção definida. Use mysqldump e autenticação por arquivo de opções protegido ou login-path; não escreva senhas na linha de comando nem no Git. Restaure em banco separado, valide migrations, registros e downloads antes de considerar o backup útil. Infraestrutura, retenção e acesso precisam de decisão do responsável pelo ambiente.

FluxoEscritaFilter usa GET_LOCK/RELEASE_LOCK em MySQL, por nome derivado do banco/prefixo, após autenticação/autorização. O lock cobre a requisição de escrita e é liberado depois da resposta ou no encerramento. Timeout de 5s devolve 503 sem executar o Controller; aguarde antes de tentar novamente. Esta estratégia simples limita a vazão das escritas e foi escolhida para o protótipo. Se houver múltiplos servidores MySQL ou escala maior, o mecanismo deve ser revisto.

Falha na gravação da auditoria é tratada e registrada em log crítico sem conteúdo clínico. A interface avisa que a operação foi processada e não deve ser repetida. Uma alteração já confirmada não é desfeita só porque o log falhou. Monitore esses eventos e proteja writable/logs; a trilha não possui garantia de imutabilidade ou atomicidade integral.

Antes de uso real: validação multiprofissional, políticas de acesso/retenção, backup, contingência e segurança de infraestrutura. O protótipo não é um prontuário homologado. NEWS2 implementa a escala 1 de saturação e não deve ser aplicado como se cobrisse escala 2; gestantes e menores de 16 anos são excluídos. As cores são seleção manual, sem fluxogramas Manchester completos. Notificações não são transmitidas ao SINAN e não há integração RNDS/SNCR.

Referência técnica do lock: https://dev.mysql.com/doc/refman/8.4/en/locking-functions.html
