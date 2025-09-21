# Lab_Manager

Um sistema web para gerenciamento de recursos de laboratórios, aulas, professores, monitores e agendamentos. Desenvolvido em PHP, oferece um dashboard para visualização em tempo real do status dos laboratórios e ferramentas administrativas para a gestão completa dos recursos.

## Funcionalidades

*   **Autenticação de Usuários:** Login, Logout e Registro para usuários autorizados.
*   **Controle de Acesso Baseado em Papéis (RBAC):** Diferenciação entre usuários administradores e comuns.
*   **Dashboard:** Exibe o status em tempo real dos laboratórios (ocupado/livre) com base no dia e turno atuais.
*   **Gerenciamento de Agendamentos:** Permite a criação e gestão de agendamentos de laboratórios.
*   **Gerenciamento de Laboratórios:** Cadastro e administração dos laboratórios disponíveis.
*   **Gerenciamento de Monitores:** Cadastro e administração dos monitores.
*   **Gerenciamento de Professores:** Cadastro e administração dos professores.
*   **Gerenciamento de Aulas:** Cadastro e administração das aulas, incluindo arquivamento automático de aulas passadas.

## Tecnologias Utilizadas

*   **Backend:** PHP
*   **Banco de Dados:** MySQL / MariaDB (utilizando PDO para conexão)
*   **Frontend:** HTML5, CSS3
*   **Ícones:** Font Awesome

## Pré-requisitos

Para executar este projeto, você precisará ter instalado:

*   Um servidor web (Apache, Nginx, etc.) com **PHP 7.4+**.
*   Um servidor de banco de dados **MySQL** ou **MariaDB**.
*   Ferramentas como **XAMPP**, **WAMP** ou **MAMP** são recomendadas para um ambiente de desenvolvimento local.

## Instalação e Configuração

Siga os passos abaixo para configurar o projeto em seu ambiente:

1.  **Clone o Repositório:**
    ```bash
    git clone <https://github.com/MarcusVinicius-BCC/Lab_Manager.git>
    cd lab_manager_php
    ```


2.  **Configurar o Banco de Dados:**
    *   Crie um banco de dados chamado `lab_manager` no seu servidor MySQL/MariaDB.
    *   Importe o arquivo `lab_manager.sql` para este banco de dados. Este arquivo contém a estrutura das tabelas e dados iniciais.
    *   Edite o arquivo `db.php` na raiz do projeto com suas credenciais de acesso ao banco de dados:
        ```php
        <?php
        $host = 'localhost'; // Geralmente 'localhost'
        $usuario = 'root';   // Seu usuário do banco de dados
        $senha = 'sua_senha'; // Sua senha do banco de dados
        $banco = 'lab_manager'; // Nome do banco de dados
        // ... restante do código
        ?>
        ```
        *(Altere `sua_senha` para a senha do seu usuário `root` ou do usuário que você configurou para o banco de dados `lab_manager`)*

3.  **Configurar o Servidor Web:**
    *   Certifique-se de que seu servidor web esteja configurado para apontar para a pasta `lab_manager_php` como o diretório raiz do seu projeto.

## Como Usar

### Acessando o Sistema

Após a instalação, você pode acessar o sistema através do seu navegador, digitando o endereço configurado para o seu servidor web (ex: `http://localhost/lab_manager_php`).

### Registro de Usuário Administrador

Para cadastrar um novo usuário com permissões de administrador, siga estes dois passos:

1.  **Autorizar a Matrícula no Banco de Dados:**
    Antes de registrar, a matrícula do novo usuário deve ser adicionada à tabela `matriculas_autorizadas`. Execute o seguinte comando SQL no seu gerenciador de banco de dados (ex: phpMyAdmin):
    ```sql
    INSERT INTO matriculas_autorizadas (matricula, usada) VALUES ('SUA_NOVA_MATRICULA', 0);
    ```
    Substitua `'SUA_NOVA_MATRICULA'` pela matrícula que você deseja autorizar.

2.  **Registrar o Usuário via Formulário:**
    Acesse a página de registro do sistema (`http://localhost/lab_manager_php/registrar.php`) e preencha o formulário com os dados do novo usuário, utilizando a mesma matrícula que você acabou de autorizar no banco de dados. O sistema irá automaticamente "hashear" a senha e atribuir a função de administrador.

### Navegação

*   **Dashboard:** Visualize o status atual dos laboratórios.
*   **Agendamento:** Realize novos agendamentos.
*   **Área Administrativa (apenas para admins):** Acesse os links no menu para gerenciar Laboratórios, Monitores, Professores e Aulas.

## Estrutura do Banco de Dados (Tabelas Principais)

*   `usuarios`: Armazena informações dos usuários, incluindo nome de usuário, hash da senha e papel (`admin`, `comum`).
*   `matriculas_autorizadas`: Lista de matrículas que podem ser usadas para registro no sistema.
*   `laboratorios`: Detalhes dos laboratórios (nome, capacidade, número, etc.).
*   `aulas`: Informações sobre as aulas (disciplina, professor, laboratório, turno, etc.).
*   `agendamentos`: Registros de agendamentos de laboratórios.
*   `professores`: Cadastro de professores.
*   `monitores`: Cadastro de monitores.
