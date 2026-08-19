<?php
/**
 * ARQUIVO: includes/banco_ficticio.php
 * OBJETIVO: Centralizar o acesso aos dados do sistema.
 * No futuro, mudaremos o miolo destas funções 
 * para conectar ao MySQL.
 */

//Função auxiliar interna para ler o arquivo 
//JSON e devolver como um array
function lerBancoJson(){
    $caminho = "data/produtos.json";
    if (!file_exists($caminho)){
        return [];
    }
    $conteudo = file_get_contents($caminho);
    return json_decode($conteudo, true) ?? [];
}
//retornar todos os produtos para a vitrine
function listarProdutos(){
    return lerBancoJson();
}

function buscarProdutoPorId($id){
    $produtos = lerBancoJson();

    foreach ($produtos as $p) {
        if ($p['id'] == $id) {
            return $p; //encontrou o produto e retorna ele
        }
    }
    return null; // se rodar o loop todo e não achar nada
}

/**
 * Salva a lista completa de produtos de volta no arquivo JSON.
 * Usada internamente por darBaixaEstoque() para persistir o novo estoque.
 */
function salvarProdutosJson($produtos) {
    $caminho = "data/produtos.json";
    $jsonTexto = json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $jsonTexto) !== false;
}

/**
 * Diminui o estoque de um produto na quantidade informada (chamada quando o
 * cliente CONFIRMA o pedido em pages/finalizar.php). Retorna true se conseguiu
 * dar baixa, false se o produto não existe ou não há unidades suficientes.
 */
function darBaixaEstoque($id, $quantidade = 1) {
    $produtos = lerBancoJson();
    $deu_baixa = false;

    foreach ($produtos as $indice => $p) {
        if ((int) $p['id'] === (int) $id) {
            $estoque_atual = (int) ($p['estoque'] ?? 0);

            if ($estoque_atual >= $quantidade) {
                $produtos[$indice]['estoque'] = $estoque_atual - $quantidade;
                $deu_baixa = true;
            }
            break;
        }
    }

    if ($deu_baixa) {
        salvarProdutosJson($produtos);
    }

    return $deu_baixa;
}

/**
 * Verifica (sem alterar nada) se há estoque suficiente de um produto para a
 * quantidade informada. Usada em finalizar.php para checar todo o carrinho
 * antes de confirmar o pedido e dar baixa no estoque.
 */
function verificarEstoqueDisponivel($id, $quantidade = 1) {
    $produto = buscarProdutoPorId($id);
    if (!$produto) {
        return false;
    }
    $estoque_atual = (int) ($produto['estoque'] ?? 0);
    return $estoque_atual >= $quantidade;
}

// FUNÇÕES PARA GERENCIAMENTO DE USUÁRIOS
//=============================================
function listarUsuarios(){
    $caminho = "../data/usuarios.json";
    if (!file_exists($caminho)){
        return [];
    }
    $conteudo = file_get_contents($caminho);
    return json_decode($conteudo, true) ?? [];
}

//FUNÇÃO PARA SALVAR UM NOVO USUÁRIO COM SENHA CRIPTOGRAFADA
function salvarUsuario($novoUsuario) {
    $caminho = "../data/usuarios.json";
    $usuariosAtuais = listarUsuarios();

    //Logica de ID automatico
    if(!empty($usuariosAtuais)){
        $ultimo = end($usuariosAtuais);
        $novoUsuario['id'] = $ultimo['id'] +1;
    } else {
        $novoUsuario['id'] = 1;
    }
    //CRUCIAL PARA SEGURANÇA: Criptografia a senha antes de salvar no JSON
    //O algoritmo PASSWORD_default gera uma chave segura de 60 caracteres(BYCRYPT).
    $novoUsuario['senha'] = password_hash($novoUsuario['senha'], PASSWORD_DEFAULT);
    $usuariosAtuais[] = $novoUsuario;
    $jsonTexto = json_encode($usuariosAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $jsonTexto) !== false;
}
// Busca um usuário específico pelo ID
function buscarUsuarioPorId($id) {
    $usuarios = listarUsuarios();
    foreach ($usuarios as $u) {
        if ($u['id'] == $id) {
            return $u;
        }
    }
    return null;
}
// Salva as alterações de um usuário existente (EDITAR ou INATIVAR)
function atualizarUsuario($id, $dadosAtualizados) {
    $caminho = "../data/usuarios.json";
    $usuarios = listarUsuarios();    
    foreach ($usuarios as $chave => $u) {
        if ($u['id'] == $id) {            
            // Se no formulário de edição o usuário digitou uma nova senha,
            // precisamos criptografá-la antes de salvar
            if (!empty($dadosAtualizados['senha'])) {
                $dadosAtualizados['senha'] = password_hash($dadosAtualizados['senha'], PASSWORD_DEFAULT);
            } else {
                // Se o campo senha veio vazio, removemos do array para manter a senha antiga
                unset($dadosAtualizados['senha']);
            }            
            // Mescla os dados antigos do usuário com as alterações novas
            $usuarios[$chave] = array_merge($u, $dadosAtualizados);            
            // Salva a lista completa atualizada no arquivo JSON
            $jsonTexto = json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

function listarCategorias(){
    $caminho = "../data/categorias.json";
    if (!file_exists($caminho)){
        return [];
    }
    $conteudo = file_get_contents($caminho);
    return json_decode($conteudo, true) ?? [];
}

function salvarCategoria($novaCategoria) {
    $caminho = "../data/categorias.json";
    $categoriasAtuais = listarCategorias();

    //Logica de ID automatico
    if(!empty($categoriasAtuais)){
        $ultimo = end($categoriasAtuais);
        $novaCategoria['id'] = $ultimo['id'] +1;
    } else {
        $novaCategoria['id'] = 1;
    }
    
    $categoriasAtuais[] = $novaCategoria;
    $jsonTexto = json_encode($categoriasAtuais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $jsonTexto) !== false;
}

function buscarCategoriaPorId($id) {
    $categorias = listarCategorias();
    foreach ($categorias as $c) {
        if ($c['id'] == $id) {
            return $c;
        }
    }
    return null;
}

function atualizarCategoria($id, $dadosAtualizados) {
    $caminho = "../data/categorias.json";
    $categorias = listarCategorias();    
    foreach ($categorias as $chave => $u) {
        if ($u['id'] == $id) {            
                      
            // Mescla os dados antigos do usuário com as alterações novas
            $categorias[$chave] = array_merge($u, $dadosAtualizados);            
            // Salva a lista completa atualizada no arquivo JSON
            $jsonTexto = json_encode($categorias, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($caminho, $jsonTexto) !== false;
        }
    }
    return false;
}

// ... (mantenha as outras funções de usuários e categorias acima) ...

/**
 * Lê e retorna a lista de fornecedores cadastrados no arquivo JSON
 */
function listarFornecedores() {
    $caminho = "data/fornecedores.json";
    if (!file_exists($caminho)) {
        $caminho = "../data/fornecedores.json";
    }

    if (!file_exists($caminho)) {
        return [];
    }

    $conteudo = file_get_contents($caminho);
    return json_decode($conteudo, true) ?? [];
}

/**
 * Cadastra um novo fornecedor com ID incremental automático
 */
function salvarFornecedor($novoFornecedor) {
    $caminho = "data/fornecedores.json";
    if (!file_exists($caminho)) {
        $caminho = "../data/fornecedores.json";
    }

    $fornecedoresExistentes = listarFornecedores();

    // Lógica do ID Auto-Incremental
    $maiorId = 0;
    foreach ($fornecedoresExistentes as $f) {
        if (isset($f['id']) && $f['id'] > $maiorId) {
            $maiorId = $f['id'];
        }
    }
    $novoFornecedor['id'] = $maiorId + 1;

    // Sanitização simples dos campos de texto para evitar quebras
    foreach ($novoFornecedor as $campo => $valor) {
        if (is_string($valor)) {
            $novoFornecedor[$campo] = htmlspecialchars(trim($valor));
        }
    }

    // Adiciona o novo fornecedor à lista
    $fornecedoresExistentes[] = $novoFornecedor;

    // Grava de volta no JSON formatado
    $jsonTexto = json_encode($fornecedoresExistentes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $jsonTexto) !== false;
}
// ... (mantenha as outras funções acima) ...

/**
 * Busca um único fornecedor pelo ID
 */
function buscarFornecedorPorId($id) {
    $fornecedores = listarFornecedores();
    foreach ($fornecedores as $f) {
        if ($f['id'] == $id) {
            return $f;
        }
    }
    return null;
}

/**
 * Atualiza os dados de um fornecedor existente
 */
function atualizarFornecedor($id, $dadosAtualizados) {
    $caminho = "data/fornecedores.json";
    if (!file_exists($caminho)) {
        $caminho = "../data/fornecedores.json";
    }

    $fornecedores = listarFornecedores();
    $encontrou = false;

    foreach ($fornecedores as $chave => $f) {
        if ($f['id'] == $id) {
            // Atualiza todos os campos mantendo o ID original
            $fornecedores[$chave]['nome']     = htmlspecialchars(trim($dadosAtualizados['nome']));
            $fornecedores[$chave]['cnpj']     = htmlspecialchars(trim($dadosAtualizados['cnpj']));
            $fornecedores[$chave]['telefone'] = htmlspecialchars(trim($dadosAtualizados['telefone']));
            $fornecedores[$chave]['rua']      = htmlspecialchars(trim($dadosAtualizados['rua']));
            $fornecedores[$chave]['numero']   = htmlspecialchars(trim($dadosAtualizados['numero']));
            $fornecedores[$chave]['bairro']   = htmlspecialchars(trim($dadosAtualizados['bairro']));
            $fornecedores[$chave]['cidade']   = htmlspecialchars(trim($dadosAtualizados['cidade']));
            $fornecedores[$chave]['estado']   = htmlspecialchars(trim($dadosAtualizados['estado']));
            $encontrou = true;
            break;
        }
    }

    if ($encontrou) {
        $jsonTexto = json_encode($fornecedores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($caminho, $jsonTexto) !== false;
    }
    return false;
}

/**
 * Exclui um fornecedor pelo ID
 */
function excluirFornecedor($id) {
    $caminho = "data/fornecedores.json";
    if (!file_exists($caminho)) {
        $caminho = "../data/fornecedores.json";
    }

    $fornecedores = listarFornecedores();
    $novoArray = [];
    $encontrou = false;

    foreach ($fornecedores as $f) {
        if ($f['id'] == $id) {
            $encontrou = true;
            continue;
        }
        $novoArray[] = $f;
    }

    if ($encontrou) {
        $jsonTexto = json_encode($novoArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($caminho, $jsonTexto) !== false;
    }
    return false;
}
?>