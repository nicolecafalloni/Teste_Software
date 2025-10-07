Estar no diretório correto da aplicação e instalar as dependências:
npm install

Para a realização dos testes, inicie o Apache e importe o banco acme.sql no phpMyadmin;

Em seguida, nas variávels das URLs do site, troque nos arquivos de teste 
(tanto no teste de login, quanto no de cadastro) para a sua URL atual:

const TARGET_URL = "URL do seu site";

Por último, no terminal do VScode, coloque o seguinte sintaxe:

node testeSoftware.js
node testeSoftwareCadastro.js