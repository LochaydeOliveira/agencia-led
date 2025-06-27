# URLs Amigáveis - Fornecedores Secretos

## Conversão HTML para PHP

O arquivo `fornecedores-secretos.html` foi convertido para `fornecedores-secretos.php` com suporte completo a URLs amigáveis.

## URLs Disponíveis

### Página Principal
- **URL**: `/` ou `/fornecedores-secretos.php`
- **Descrição**: Página principal com todos os produtos

### Produtos Específicos
- **Autopeças e Ferramentas**: `/autopecas-ferramentas`
- **Moda Feminina**: `/moda-feminina`
- **Moda Masculina**: `/moda-masculina`
- **Semijoias**: `/semijoias`

## Funcionalidades Implementadas

### 1. Roteamento Inteligente
- O sistema detecta automaticamente qual página deve ser exibida baseado na URL
- Suporte a páginas de produtos individuais
- Página 404 personalizada para URLs não encontradas

### 2. Meta Tags Dinâmicas
- Títulos e descrições otimizados para SEO
- Open Graph tags para compartilhamento em redes sociais
- Meta tags específicas para cada produto

### 3. URLs Amigáveis
- URLs limpas e fáceis de lembrar
- Redirecionamento automático de URLs antigas
- Suporte a parâmetros GET para funcionalidades futuras

### 4. Performance
- Compressão GZIP ativada
- Cache de navegador configurado
- Lazy loading de imagens mantido

## Como Usar

### Acessar Página Principal
```
https://seudominio.com/
https://seudominio.com/fornecedores-secretos.php
```

### Acessar Produto Específico
```
https://seudominio.com/autopecas-ferramentas
https://seudominio.com/moda-feminina
https://seudominio.com/moda-masculina
https://seudominio.com/semijoias
```

## Configuração do Servidor

### Apache (.htaccess)
O arquivo `.htaccess` já está configurado com:
- Redirecionamentos 301 para URLs antigas
- Regras de rewrite para URLs amigáveis
- Compressão GZIP
- Cache de navegador

### Nginx (se necessário)
```nginx
location / {
    try_files $uri $uri/ /fornecedores-secretos.php?$args;
}

location ~ ^/(autopecas-ferramentas|moda-feminina|moda-masculina|semijoias)/?$ {
    try_files $uri $uri/ /fornecedores-secretos.php?$args;
}
```

## Estrutura do Código

### Variáveis Principais
- `$page_type`: Define o tipo de página (home, produto, 404)
- `$produto_encontrado`: Contém dados do produto quando aplicável
- `$produtos`: Array com todos os produtos disponíveis

### Funções
- `generateFriendlyUrl()`: Converte strings em URLs amigáveis
- Roteamento automático baseado na URL

## Benefícios

1. **SEO Melhorado**: URLs mais amigáveis para motores de busca
2. **UX Aprimorada**: URLs mais fáceis de lembrar e compartilhar
3. **Manutenibilidade**: Código organizado e fácil de expandir
4. **Performance**: Otimizações de cache e compressão
5. **Flexibilidade**: Fácil adição de novos produtos

## Próximos Passos

Para expandir o sistema, você pode:
1. Conectar com banco de dados para produtos dinâmicos
2. Adicionar sistema de categorias
3. Implementar busca de produtos
4. Adicionar sistema de carrinho de compras
5. Integrar com APIs de pagamento

## Suporte

O código está documentado e pronto para uso. Todas as funcionalidades do HTML original foram preservadas e melhoradas com recursos PHP. 