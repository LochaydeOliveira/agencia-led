// Função para carregar o arquivo JSON e preencher os cards
fetch('assets-agencia-led/conteudo-cards.json')
  .then(response => response.json())
  .then(data => {
    const cardContainer = document.getElementById('card-container');
    
    // Loop para criar os cards a partir do JSON
    data.cards.forEach(card => {
      const cardElement = document.createElement('div');
      cardElement.classList.add('col');
      
      cardElement.innerHTML = `
        <div class="border-0 card h-100 p-lg-4 rounded-5 shadow-lg">
          <div class="card-body">
            <h5 class="card-title fw-bold h5-agl-plans-tt">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-shop-window" viewBox="0 0 16 16">
                <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5m2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5"/>
              </svg>
              ${card.title}
            </h5>
            <div class="mt-3">
              <p class="mb-1 price-main"><span>R$</span>${card.price}</p>
            </div>
            <span class="sub-tt-plans">${card.subTitle}</span>
            <span class="sub-tt-plans-expl">${card.description}</span>
            <p class="mb-1 mg-left-gd"><strong>Recursos em destaque</strong></p>
            <ul class="list-group list-group-flush p-tp-dw">
              ${card.features.map(feature => `<li class="list-group-item d-flex align-items-center font-sz-itns-pls">
                <span class="me-2">
                  <img src="assets-agencia-led/icones-svg/check.svg" alt="Icone SVG">
                </span>
                ${feature}
              </li>`).join('')}
            </ul>
            <a href="https://wa.me/message/ZLMXHQAQ5FW5P1" class="btn-pay btn btn-section-primary btn-lg" target="_blank">CRIAR LOJA</a>
            <p class="card-text des-plans">
              Pagamento via Pix <img src="assets-agencia-led/icones-svg/icons8-foto-vazado.svg" alt="">
            </p>
            <p class="card-text text-contions">
              Condições: 50% no Início / 50% na Entrega
            </p>
          </div>
        </div>
      `;
      cardContainer.appendChild(cardElement);
    });
  })
  .catch(error => console.error('Erro ao carregar o arquivo JSON:', error));



function carregarElemento(id, arquivo) {
  fetch(arquivo)
    .then(response => response.text())
    .then(data => {
      document.getElementById(id).innerHTML = data;
    })
    .catch(error => console.error('Erro ao carregar o arquivo:', error));
}

carregarElemento('header', 'header.html');
carregarElemento('footer', 'footer.html');

