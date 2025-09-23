# Lab Manager PHP

![Logo do Projeto](img/logo.png)

## Descrição do Projeto

O Lab Manager PHP é um sistema web desenvolvido em PHP para gerenciar laboratórios, agendamentos, aulas, professores e monitores. Ele oferece uma interface intuitiva para visualizar o status dos laboratórios, agendar horários e gerenciar informações relacionadas ao ambiente acadêmico. O sistema foi projetado com uma abordagem mobile-first, garantindo uma experiência de usuário otimizada em diversos dispositivos.

## Funcionalidades

*   **Gestão de Laboratórios:** Cadastro e visualização do status (livre/ocupado) dos laboratórios.
*   **Agendamento de Horários:** Permite agendar laboratórios para aulas e outros eventos.
*   **Gestão de Aulas:** Cadastro e acompanhamento das aulas que utilizam os laboratórios.
*   **Gestão de Professores:** Cadastro e informações de contato dos professores.
*   **Gestão de Monitores:** Cadastro e informações dos monitores.
*   **Calendário Interativo:** Visualização de agendamentos e aulas em um calendário.
*   **Autenticação de Usuários:** Sistema de login e registro para acesso seguro.
*   **Responsividade:** Interface adaptável a diferentes tamanhos de tela (desktops, tablets e smartphones) com design mobile-first.
*   **Regimento Interno:** Página dedicada a exibir o regimento ou regras de uso dos laboratórios.

## Tecnologias Utilizadas

*   **Backend:** PHP
*   **Banco de Dados:** MySQL (o arquivo `lab_manager.sql` contém a estrutura do banco de dados)
*   **Frontend:** HTML5, CSS3 (com design responsivo mobile-first), JavaScript (se houver, para interatividade)
*   **Estilização:** Fontes Google Fonts (Oswald, Poppins)

## Estrutura do Projeto

```
lab_manager_php/
├── .gitignore
├── agenda.php             # Página para visualização e gestão da agenda/calendário
├── agendamentos.php       # Página para gestão de agendamentos
├── aulas.php              # Página para gestão de aulas
├── db.php                 # Configuração de conexão com o banco de dados
├── index.php              # Página inicial/Dashboard
├── informacoes.php        # Página com informações sobre os laboratórios (fotos, etc.)
├── lab_manager.sql        # Script SQL para criação do banco de dados e tabelas
├── laboratorios.php       # Página para gestão de laboratórios
├── login.php              # Página de login
├── logout.php             # Script para deslogar o usuário
├── monitores.php          # Página para gestão de monitores
├── password_hasher.php    # Utilitário para hash de senhas
├── professores.php        # Página para gestão de professores
├── README.md              # Este arquivo README
├── regimento.php          # Página com o regimento interno
├── registrar.php          # Página de registro de novos usuários
├── .git/...               # Diretório de controle de versão Git
├── css/
│   └── style.css          # Folha de estilos principal (com responsividade mobile-first)
└── img/
    ├── Lab_Dev.png
    ├── Lab_Inova.png
    ├── Lab_Prog.png
    ├── LabDev.png
    ├── LabInova.png
    ├── LabProg.png
    ├── logo.png             # Logo do projeto
    └── ...                  # Outras imagens
```

## Como Instalar e Rodar

Para configurar e executar o projeto em seu ambiente local, siga os passos abaixo:

### Pré-requisitos

Certifique-se de ter os seguintes softwares instalados:

*   **Servidor Web:** Apache (geralmente incluído em pacotes como XAMPP, WAMP, MAMP)
*   **Interpretador PHP:** PHP 7.x ou superior
*   **Servidor de Banco de Dados:** MySQL

### 1. Clonar o Repositório

```bash
git clone <URL_DO_SEU_REPOSITORIO>
cd lab_manager_php
```

### 2. Configuração do Banco de Dados

1.  Acesse o seu gerenciador de banco de dados (ex: phpMyAdmin, MySQL Workbench).
2.  Crie um novo banco de dados com o nome `lab_manager` (ou o nome que preferir).
3.  Importe o arquivo `lab_manager.sql` para o banco de dados recém-criado. Este arquivo contém a estrutura das tabelas e dados iniciais.
4.  Abra o arquivo `db.php` e configure as credenciais do seu banco de dados:

    ```php
    <?php
    $servername = "localhost";
    $username = "seu_usuario_mysql"; // Ex: root
    $password = "sua_senha_mysql";   // Ex: (vazio) ou sua_senha
    $dbname = "lab_manager";

    // Cria conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }
    ?>
    ```

### 3. Configurar o Servidor Web

1.  Mova a pasta `lab_manager_php` para o diretório `htdocs` do seu servidor Apache (ou o diretório raiz do seu servidor web).
2.  Certifique-se de que o Apache e o MySQL estejam em execução.

### 4. Acessar o Sistema

Abra seu navegador e acesse:

```
http://localhost/lab_manager_php
```

Ou o caminho correspondente à sua configuração.

## A página login aceita apenas cadastro de usuários com a matricula autorizada
 
 * Se deve executar o seginte comando no banco de dados: INSERT INTO matriculas_autorizadas (matricula) VALUES ('SUA_MATRICULA_AQUI');

## Usuário administrador cadastrado
 * LOGIN: admin
 * SENHA: admin

## Uso

Após a instalação, você pode:

1.  **Registrar** um novo usuário através da página `registrar.php`.
2.  **Fazer Login** com suas credenciais na página `login.php`.
3.  Navegar pelo **Dashboard** (`index.php`) para ver o status dos laboratórios.
4.  Utilizar o **Menu de Navegação** para acessar as diferentes seções: Agendamentos, Aulas, Laboratórios, Professores, Monitores, Agenda e Informações.
5.  Consultar o **Regimento** na página `regimento.php`.

## Responsividade (Mobile-First)

O projeto foi desenvolvido seguindo o princípio de *Mobile-First Design*. Isso significa que a estilização foi iniciada e otimizada para telas de dispositivos móveis, e então, através de *media queries* CSS (`@media (min-width: 768px)`), os estilos são progressivamente aprimorados para tablets e desktops. Isso garante uma experiência de usuário consistente e agradável em qualquer tamanho de tela.

## Contribuição

Contribuições são bem-vindas! Se você deseja contribuir com o projeto, siga os passos:

1.  Faça um fork do repositório.
2.  Crie uma nova branch (`git checkout -b feature/sua-feature`).
3.  Faça suas alterações e commit (`git commit -m 'Adiciona nova feature'`).
4.  Envie para a branch original (`git push origin feature/sua-feature`).
5.  Abra um Pull Request.

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo `LICENSE` (se existir) para mais detalhes.

## Contato

Para dúvidas ou sugestões, entre em contato com [Marcus Vinicius Campos Da Siva/ marcusufopa@gmail.com]
========================================================================================