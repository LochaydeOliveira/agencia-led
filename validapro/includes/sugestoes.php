<?php
// Sugestões de nichos populares para dropshipping
$nichos_populares = [
    'fitness' => [
        'nome' => 'Fitness e Saúde',
        'promessas' => [
            'Transformar seu corpo em 30 dias',
            'Perder peso de forma saudável e rápida',
            'Ganhar massa muscular sem academia'
        ],
        'cliente_consciente' => [
            'Sim, o cliente já sabe que precisa melhorar a forma física',
            'Parcialmente, o cliente quer resultados mas não sabe como',
            'O cliente está frustrado com dietas que não funcionam'
        ],
        'beneficios' => [
            'Melhora a saúde e bem-estar',
            'Aumenta a autoestima',
            'Economiza tempo e dinheiro da academia'
        ],
        'mecanismos' => [
            'Tecnologia exclusiva de treinamento',
            'Método comprovado cientificamente',
            'Sistema personalizado de exercícios'
        ]
    ],
    'beleza' => [
        'nome' => 'Beleza e Cuidados',
        'promessas' => [
            'Pele perfeita em 7 dias',
            'Cabelo sedoso e brilhante',
            'Maquiagem profissional em casa'
        ],
        'cliente_consciente' => [
            'Sim, o cliente já busca produtos de beleza',
            'Parcialmente, o cliente quer melhorar mas não sabe qual produto usar',
            'O cliente está insatisfeito com produtos atuais'
        ],
        'beneficios' => [
            'Aumenta a confiança',
            'Economiza dinheiro de salões',
            'Resultados rápidos e duradouros'
        ],
        'mecanismos' => [
            'Ingredientes naturais exclusivos',
            'Tecnologia avançada de tratamento',
            'Método único de aplicação'
        ]
    ],
    'casa' => [
        'nome' => 'Casa e Organização',
        'promessas' => [
            'Casa organizada em 1 dia',
            'Espaço otimizado e funcional',
            'Limpeza rápida e eficiente'
        ],
        'cliente_consciente' => [
            'Sim, o cliente já sabe que precisa organizar a casa',
            'Parcialmente, o cliente quer organização mas não sabe por onde começar',
            'O cliente está sobrecarregado com a desorganização'
        ],
        'beneficios' => [
            'Mais tempo livre',
            'Menos estresse',
            'Casa mais confortável'
        ],
        'mecanismos' => [
            'Sistema modular de organização',
            'Produto multifuncional',
            'Design inovador e prático'
        ]
    ],
    'tecnologia' => [
        'nome' => 'Tecnologia e Gadgets',
        'promessas' => [
            'Produtividade máxima',
            'Tecnologia acessível',
            'Inovação para todos'
        ],
        'cliente_consciente' => [
            'Sim, o cliente já busca soluções tecnológicas',
            'Parcialmente, o cliente quer tecnologia mas não sabe qual escolher',
            'O cliente está frustrado com produtos tecnológicos complexos'
        ],
        'beneficios' => [
            'Aumenta a produtividade',
            'Facilita tarefas diárias',
            'Tecnologia de ponta'
        ],
        'mecanismos' => [
            'Tecnologia exclusiva',
            'Patente registrada',
            'Inovação revolucionária'
        ]
    ],
    'pet' => [
        'nome' => 'Pet e Animais',
        'promessas' => [
            'Pet mais feliz e saudável',
            'Cuidados profissionais em casa',
            'Bem-estar animal garantido'
        ],
        'cliente_consciente' => [
            'Sim, o cliente já busca produtos para o pet',
            'Parcialmente, o cliente quer cuidar do pet mas não sabe como',
            'O cliente está preocupado com a saúde do pet'
        ],
        'beneficios' => [
            'Pet mais saudável',
            'Economiza veterinário',
            'Mais tempo de qualidade'
        ],
        'mecanismos' => [
            'Tecnologia veterinária',
            'Ingredientes naturais',
            'Método comprovado'
        ]
    ]
];

// Templates de respostas por categoria
$templates_respostas = [
    'promessa_principal' => [
        'Transformar a vida do cliente de forma rápida e eficaz',
        'Resolver um problema específico de forma definitiva',
        'Economizar tempo e dinheiro do cliente',
        'Melhorar a qualidade de vida',
        'Oferecer uma solução única e inovadora'
    ],
    'cliente_consciente' => [
        'Sim, o cliente já sabe que tem o problema e busca soluções',
        'Parcialmente, o cliente sente o problema mas não sabe como resolver',
        'Não, preciso educar o cliente sobre o problema',
        'O cliente está frustrado com soluções existentes',
        'O cliente busca melhorias contínuas'
    ],
    'beneficios' => [
        'Economia de tempo, dinheiro e esforço',
        'Melhora a qualidade de vida e bem-estar',
        'Resolve problemas específicos de forma definitiva',
        'Aumenta a produtividade e eficiência',
        'Oferece conveniência e praticidade'
    ],
    'mecanismo_unico' => [
        'Tecnologia exclusiva ou patenteada',
        'Método ou processo único',
        'Combinação única de características',
        'Design inovador e revolucionário',
        'Sistema proprietário exclusivo'
    ]
];

// Função para obter sugestões por nicho
function getSugestoesNicho($nicho) {
    global $nichos_populares;
    return $nichos_populares[$nicho] ?? null;
}

// Função para obter templates de resposta
function getTemplatesResposta($campo) {
    global $templates_respostas;
    return $templates_respostas[$campo] ?? [];
}

// Função para obter todos os nichos
function getAllNichos() {
    global $nichos_populares;
    return $nichos_populares;
}
?> 