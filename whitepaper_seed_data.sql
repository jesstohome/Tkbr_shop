-- ============================================================
-- 巴西电商平台白皮书初始化数据 v3
-- 品牌中立 / Português (Brasil) + English
-- 更完整的白皮书内容 + 更丰富的 HTML 排版
--
-- 使用说明：
-- 1. 如果已经导入过 1001~1003，请先执行下面两条 DELETE
-- 2. 然后执行本文件
-- 3. cover_image 可在后台上传
-- ============================================================

DELETE FROM `whitepaper_translations`
WHERE `whitepaper_id` IN (1001, 1002, 1003);

DELETE FROM `whitepapers`
WHERE `id` IN (1001, 1002, 1003);


-- ============================================================
-- 白皮书主表
-- ============================================================

INSERT INTO `whitepapers`
(`id`, `slug`, `cover_image`, `status`, `views`, `sort`, `created_at`, `updated_at`)
VALUES
(1001, 'platform-overview', NULL, 1, 0, 0, NOW(), NOW()),
(1002, 'seller-guide', NULL, 1, 0, 1, NOW(), NOW()),
(1003, 'brazil-ecommerce-market', NULL, 1, 0, 2, NOW(), NOW());


-- ============================================================
-- 白皮书 1：平台概览 - 葡萄牙语
-- ============================================================

INSERT INTO `whitepaper_translations`
(`whitepaper_id`, `lang`, `title`, `summary`, `content`, `pdf`, `created_at`, `updated_at`)
VALUES
(1001, 'pt',
'Visão Geral da Plataforma',
'Conheça a estrutura do nosso marketplace, o ecossistema de vendedores e clientes, o fluxo completo de pedidos, a gestão financeira, os processos de operação e os principais recursos desenvolvidos para apoiar o crescimento do comércio digital no Brasil.',
'
<div style="background: linear-gradient(135deg, #102a43 0%, #0f5c46 55%, #1f8a68 100%); border-radius: 20px; padding: 46px 34px; margin-bottom: 34px; color: #ffffff; text-align: center;">
    <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #b9ddd0; margin-bottom: 12px;">WHITEPAPER • BRASIL</div>
    <h2 style="color: #ffffff; border: none; margin: 0 0 16px 0; font-size: 30px; line-height: 1.3;">Uma Nova Experiência para o Comércio Digital</h2>
    <p style="color: #e3f2ed; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 760px;">
        Uma plataforma criada para aproximar vendedores e consumidores, simplificar a operação de lojas digitais e oferecer uma experiência de compra mais organizada, transparente e conveniente.
    </p>
</div>

<div style="background: #f5faf8; border: 1px solid #dcebe5; border-radius: 16px; padding: 24px; margin-bottom: 30px;">
    <h3 style="color: #0f5c46; margin-top: 0; margin-bottom: 10px;">Sobre este documento</h3>
    <p style="margin: 0; color: #43564f; line-height: 1.8;">
        Este whitepaper apresenta os principais conceitos que orientam a plataforma, desde o funcionamento do marketplace e a jornada do pedido até a gestão da loja, atendimento ao cliente, acompanhamento financeiro e boas práticas de operação.
    </p>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">1. Nossa Visão</h2>

<p style="line-height: 1.85;">
    O comércio eletrônico brasileiro evoluiu de forma significativa e passou a fazer parte da rotina de milhões de consumidores. Nesse cenário, uma plataforma de marketplace precisa fazer mais do que simplesmente listar produtos.
</p>

<p style="line-height: 1.85;">
    É necessário criar uma infraestrutura que ajude vendedores a administrar seus negócios, permita que clientes encontrem produtos com facilidade e ofereça uma jornada de compra clara desde a descoberta até a entrega.
</p>

<p style="line-height: 1.85;">
    Nossa proposta é construir um ambiente digital no qual tecnologia, operação e experiência do usuário trabalhem em conjunto. O objetivo é reduzir a complexidade da gestão diária e permitir que vendedores concentrem seus esforços em produtos, atendimento e crescimento.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 28px 0;">
    <div style="flex: 1; min-width: 180px; background: #eef7f3; border-radius: 14px; padding: 22px; border: 1px solid #d8e9e1;">
        <div style="font-size: 27px; font-weight: 700; color: #0f5c46;">01</div>
        <strong style="color: #102a43;">Simplicidade</strong>
        <p style="font-size: 14px; color: #61736c; line-height: 1.6; margin-bottom: 0;">Ferramentas organizadas para facilitar a operação diária.</p>
    </div>
    <div style="flex: 1; min-width: 180px; background: #eef7f3; border-radius: 14px; padding: 22px; border: 1px solid #d8e9e1;">
        <div style="font-size: 27px; font-weight: 700; color: #0f5c46;">02</div>
        <strong style="color: #102a43;">Transparência</strong>
        <p style="font-size: 14px; color: #61736c; line-height: 1.6; margin-bottom: 0;">Informações e etapas da operação apresentadas de forma clara.</p>
    </div>
    <div style="flex: 1; min-width: 180px; background: #eef7f3; border-radius: 14px; padding: 22px; border: 1px solid #d8e9e1;">
        <div style="font-size: 27px; font-weight: 700; color: #0f5c46;">03</div>
        <strong style="color: #102a43;">Crescimento</strong>
        <p style="font-size: 14px; color: #61736c; line-height: 1.6; margin-bottom: 0;">Recursos pensados para apoiar vendedores em diferentes estágios.</p>
    </div>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">2. Como Funciona o Ecossistema</h2>

<p style="line-height: 1.85;">
    O marketplace conecta diferentes participantes em uma única infraestrutura digital. Cada parte possui responsabilidades próprias, mas todas estão integradas dentro do mesmo fluxo operacional.
</p>

<div style="background: #ffffff; border: 1px solid #dfeae5; border-radius: 16px; padding: 26px; margin-bottom: 16px;">
    <h3 style="color: #0f5c46; margin-top: 0;">Vendedores</h3>
    <p style="line-height: 1.8; margin-bottom: 0;">
        Os vendedores criam e administram suas lojas, cadastram produtos, controlam estoque, acompanham pedidos, interagem com clientes e consultam informações financeiras através do painel da plataforma.
    </p>
</div>

<div style="background: #ffffff; border: 1px solid #dfeae5; border-radius: 16px; padding: 26px; margin-bottom: 16px;">
    <h3 style="color: #0f5c46; margin-top: 0;">Clientes</h3>
    <p style="line-height: 1.8; margin-bottom: 0;">
        Os clientes podem navegar pelas categorias, comparar produtos, realizar compras, acompanhar pedidos e compartilhar sua experiência após a entrega.
    </p>
</div>

<div style="background: #ffffff; border: 1px solid #dfeae5; border-radius: 16px; padding: 26px; margin-bottom: 26px;">
    <h3 style="color: #0f5c46; margin-top: 0;">Operação e Suporte</h3>
    <p style="line-height: 1.8; margin-bottom: 0;">
        A estrutura operacional organiza processos, acompanha pedidos e oferece suporte para que vendedores possam manter suas lojas funcionando de maneira consistente.
    </p>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">3. Jornada Completa de um Pedido</h2>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 28px;">

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">1</div>
        <div>
            <strong style="color: #102a43;">Compra realizada</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">O cliente seleciona o produto, confirma o pedido e recebe as informações correspondentes à compra.</p>
        </div>
    </div>

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">2</div>
        <div>
            <strong style="color: #102a43;">Pedido recebido pelo vendedor</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">O pedido fica disponível no painel para que o vendedor acompanhe os próximos passos da operação.</p>
        </div>
    </div>

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">3</div>
        <div>
            <strong style="color: #102a43;">Preparação e liberação do envio</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">Após o cumprimento das condições operacionais aplicáveis, o pedido avança para a etapa de envio.</p>
        </div>
    </div>

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">4</div>
        <div>
            <strong style="color: #102a43;">Transporte e acompanhamento</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">O pedido segue para entrega e o vendedor pode acompanhar o status disponível no sistema.</p>
        </div>
    </div>

    <div style="display: flex;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">5</div>
        <div>
            <strong style="color: #102a43;">Entrega concluída</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">Após a confirmação da entrega e o cumprimento das regras aplicáveis, o pedido é concluído e os valores seguem o fluxo financeiro correspondente.</p>
        </div>
    </div>

</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">4. Gestão Financeira</h2>

<p style="line-height: 1.85;">
    A gestão financeira é uma das partes mais importantes da operação de uma loja. Por isso, a plataforma organiza informações relacionadas a vendas, saldo, movimentações e solicitações financeiras em um centro de controle.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #0f5c46; color: #ffffff;">
<th style="padding: 14px; text-align: left;">Área</th>
<th style="padding: 14px; text-align: left;">Objetivo</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Vendas</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Consultar valores associados aos pedidos realizados.</td>
</tr>
<tr style="background: #f5faf8;">
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Saldo</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Visualizar o saldo disponível de acordo com as regras da plataforma.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Movimentações</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Acompanhar entradas, saídas e demais registros financeiros.</td>
</tr>
<tr style="background: #f5faf8;">
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Saques</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Solicitar e acompanhar operações de retirada conforme os métodos disponíveis.</td>
</tr>
</tbody>
</table>

<div style="background: #fff8e8; border-left: 5px solid #e4a21a; border-radius: 10px; padding: 20px; margin: 24px 0;">
    <strong style="color: #795400;">Importante</strong>
    <p style="margin: 7px 0 0 0; line-height: 1.75;">
        Prazos, limites, taxas, métodos de saque e demais condições financeiras devem sempre seguir as regras vigentes apresentadas no painel da plataforma.
    </p>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">5. Segurança e Confiança</h2>

<p style="line-height: 1.85;">
    Confiança é um dos pilares de qualquer marketplace. A plataforma adota mecanismos operacionais destinados a melhorar a segurança das contas, a qualidade das informações e o acompanhamento das transações.
</p>

<ul style="line-height: 2;">
<li><strong>Verificação de informações:</strong> dados necessários para cadastro e operação devem ser fornecidos de forma correta.</li>
<li><strong>Controle de acesso:</strong> informações da conta devem ser protegidas pelo próprio usuário.</li>
<li><strong>Acompanhamento de pedidos:</strong> cada etapa relevante deve permanecer registrada no sistema.</li>
<li><strong>Monitoramento operacional:</strong> comportamentos fora dos padrões podem ser analisados de acordo com as regras aplicáveis.</li>
<li><strong>Reputação:</strong> qualidade do atendimento e experiência de compra ajudam a construir confiança.</li>
</ul>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">6. Uma Plataforma Pensada para Crescer</h2>

<p style="line-height: 1.85;">
    Pequenos vendedores precisam de simplicidade. Lojas em crescimento precisam de organização. Operações maiores precisam de controle e visibilidade. Uma infraestrutura de marketplace deve ser capaz de atender diferentes momentos do ciclo de crescimento.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
    <div style="flex: 1; min-width: 210px; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
        <h3 style="color: #0f5c46; margin-top: 0;">Começar</h3>
        <p style="line-height: 1.7; margin-bottom: 0;">Configurar a loja, publicar produtos e compreender o funcionamento da plataforma.</p>
    </div>
    <div style="flex: 1; min-width: 210px; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
        <h3 style="color: #0f5c46; margin-top: 0;">Operar</h3>
        <p style="line-height: 1.7; margin-bottom: 0;">Administrar pedidos, estoque, atendimento e informações financeiras com consistência.</p>
    </div>
    <div style="flex: 1; min-width: 210px; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
        <h3 style="color: #0f5c46; margin-top: 0;">Expandir</h3>
        <p style="line-height: 1.7; margin-bottom: 0;">Aumentar o catálogo, melhorar a experiência e desenvolver uma operação sustentável.</p>
    </div>
</div>

<div style="background: #eef7f3; border-radius: 16px; padding: 26px; margin-top: 30px;">
<h3 style="color: #0f5c46; margin-top: 0;">Perguntas Frequentes</h3>
<p><strong>Preciso ter experiência para começar?</strong><br>Não necessariamente. O importante é seguir as orientações da plataforma, manter os dados corretos e administrar a loja de forma responsável.</p>
<p><strong>Onde posso acompanhar meus pedidos?</strong><br>Os pedidos e seus respectivos status ficam disponíveis no painel da loja.</p>
<p><strong>Como acompanho minhas movimentações financeiras?</strong><br>O centro financeiro apresenta os registros disponíveis relacionados às vendas, saldo e operações financeiras.</p>
<p style="margin-bottom: 0;"><strong>O que mais influencia o crescimento da loja?</strong><br>Produtos adequados, boas imagens, descrições completas, estoque atualizado, preços competitivos, atendimento rápido e consistência operacional são fatores importantes.</p>
</div>

<div style="background: linear-gradient(135deg, #0f5c46 0%, #1f8a68 100%); border-radius: 18px; padding: 34px; margin-top: 32px; text-align: center; color: #ffffff;">
<h3 style="color: #ffffff; border: none; margin: 0 0 10px 0;">Construa Sua Próxima Etapa</h3>
<p style="margin: 0; color: #dcefe8; line-height: 1.7;">
    Uma boa operação começa com processos simples, informações claras e atenção constante à experiência do cliente.
</p>
</div>
',
NULL, NOW(), NOW());


-- ============================================================
-- 白皮书 1：平台概览 - English
-- ============================================================

INSERT INTO `whitepaper_translations`
(`whitepaper_id`, `lang`, `title`, `summary`, `content`, `pdf`, `created_at`, `updated_at`)
VALUES
(1001, 'en',
'Platform Overview',
'Explore the structure of our marketplace, the seller and customer ecosystem, the complete order journey, financial management, operational processes and the key resources designed to support digital commerce in Brazil.',
'
<div style="background: linear-gradient(135deg, #102a43 0%, #0f5c46 55%, #1f8a68 100%); border-radius: 20px; padding: 46px 34px; margin-bottom: 34px; color: #ffffff; text-align: center;">
    <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #b9ddd0; margin-bottom: 12px;">WHITEPAPER • BRAZIL</div>
    <h2 style="color: #ffffff; border: none; margin: 0 0 16px 0; font-size: 30px; line-height: 1.3;">A New Experience for Digital Commerce</h2>
    <p style="color: #e3f2ed; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 760px;">
        A platform designed to connect sellers and customers, simplify digital store operations and provide a more organized, transparent and convenient shopping experience.
    </p>
</div>

<div style="background: #f5faf8; border: 1px solid #dcebe5; border-radius: 16px; padding: 24px; margin-bottom: 30px;">
    <h3 style="color: #0f5c46; margin-top: 0; margin-bottom: 10px;">About this document</h3>
    <p style="margin: 0; color: #43564f; line-height: 1.8;">
        This whitepaper presents the key concepts behind the platform, from marketplace operations and the complete order journey to store management, customer service, financial monitoring and practical operating principles.
    </p>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">1. Our Vision</h2>

<p style="line-height: 1.85;">
    Brazilian e-commerce has become an important part of everyday consumer behavior. In this environment, a marketplace needs to offer more than a simple product catalog.
</p>

<p style="line-height: 1.85;">
    Sellers need practical tools to manage their stores, customers need a clear purchasing journey, and the entire operation needs to remain organized from discovery to delivery.
</p>

<p style="line-height: 1.85;">
    Our approach is to bring technology, operations and user experience together in one digital infrastructure. The goal is to reduce operational complexity and allow sellers to focus on products, customers and sustainable growth.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 28px 0;">
    <div style="flex: 1; min-width: 180px; background: #eef7f3; border-radius: 14px; padding: 22px; border: 1px solid #d8e9e1;">
        <div style="font-size: 27px; font-weight: 700; color: #0f5c46;">01</div>
        <strong style="color: #102a43;">Simplicity</strong>
        <p style="font-size: 14px; color: #61736c; line-height: 1.6; margin-bottom: 0;">Organized tools designed to simplify daily operations.</p>
    </div>
    <div style="flex: 1; min-width: 180px; background: #eef7f3; border-radius: 14px; padding: 22px; border: 1px solid #d8e9e1;">
        <div style="font-size: 27px; font-weight: 700; color: #0f5c46;">02</div>
        <strong style="color: #102a43;">Transparency</strong>
        <p style="font-size: 14px; color: #61736c; line-height: 1.6; margin-bottom: 0;">Clear information and visible operational stages.</p>
    </div>
    <div style="flex: 1; min-width: 180px; background: #eef7f3; border-radius: 14px; padding: 22px; border: 1px solid #d8e9e1;">
        <div style="font-size: 27px; font-weight: 700; color: #0f5c46;">03</div>
        <strong style="color: #102a43;">Growth</strong>
        <p style="font-size: 14px; color: #61736c; line-height: 1.6; margin-bottom: 0;">Resources designed for sellers at different stages.</p>
    </div>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">2. How the Ecosystem Works</h2>

<p style="line-height: 1.85;">
    The marketplace connects different participants through one digital infrastructure. Each participant has a distinct role while remaining part of the same operational flow.
</p>

<div style="background: #ffffff; border: 1px solid #dfeae5; border-radius: 16px; padding: 26px; margin-bottom: 16px;">
    <h3 style="color: #0f5c46; margin-top: 0;">Sellers</h3>
    <p style="line-height: 1.8; margin-bottom: 0;">
        Sellers create and manage their stores, publish products, maintain inventory, monitor orders, communicate with customers and review financial information through the platform dashboard.
    </p>
</div>

<div style="background: #ffffff; border: 1px solid #dfeae5; border-radius: 16px; padding: 26px; margin-bottom: 16px;">
    <h3 style="color: #0f5c46; margin-top: 0;">Customers</h3>
    <p style="line-height: 1.8; margin-bottom: 0;">
        Customers can browse categories, compare products, place orders, follow delivery progress and share their experience after receiving an order.
    </p>
</div>

<div style="background: #ffffff; border: 1px solid #dfeae5; border-radius: 16px; padding: 26px; margin-bottom: 26px;">
    <h3 style="color: #0f5c46; margin-top: 0;">Operations and Support</h3>
    <p style="line-height: 1.8; margin-bottom: 0;">
        The operational structure organizes processes, monitors orders and provides support so sellers can maintain consistent store operations.
    </p>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">3. Complete Order Journey</h2>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 28px;">

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">1</div>
        <div>
            <strong style="color: #102a43;">Customer places an order</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">The customer selects a product, confirms the order and receives the relevant purchase information.</p>
        </div>
    </div>

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">2</div>
        <div>
            <strong style="color: #102a43;">Order received by the seller</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">The order becomes available in the seller dashboard for the next operational steps.</p>
        </div>
    </div>

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">3</div>
        <div>
            <strong style="color: #102a43;">Preparation and shipping release</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">Once the applicable operational requirements are completed, the order moves forward to shipping.</p>
        </div>
    </div>

    <div style="display: flex; margin-bottom: 22px;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">4</div>
        <div>
            <strong style="color: #102a43;">Transportation and tracking</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">The order proceeds to delivery while the seller can monitor the available status information.</p>
        </div>
    </div>

    <div style="display: flex;">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #0f5c46; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-right: 15px;">5</div>
        <div>
            <strong style="color: #102a43;">Delivery completed</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.7;">After delivery confirmation and completion of the applicable rules, the order is completed and the corresponding financial flow proceeds.</p>
        </div>
    </div>

</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">4. Financial Management</h2>

<p style="line-height: 1.85;">
    Financial management is one of the most important parts of running an online store. The platform therefore organizes information related to sales, balances, transactions and financial requests through a centralized financial area.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #0f5c46; color: #ffffff;">
<th style="padding: 14px; text-align: left;">Area</th>
<th style="padding: 14px; text-align: left;">Purpose</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Sales</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Review values associated with completed orders.</td>
</tr>
<tr style="background: #f5faf8;">
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Balance</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">View the available balance according to platform rules.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Transactions</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Follow incoming, outgoing and other recorded financial movements.</td>
</tr>
<tr style="background: #f5faf8;">
<td style="padding: 13px; border: 1px solid #d8e9e1;"><strong>Withdrawals</strong></td>
<td style="padding: 13px; border: 1px solid #d8e9e1;">Request and monitor withdrawals through available methods.</td>
</tr>
</tbody>
</table>

<div style="background: #fff8e8; border-left: 5px solid #e4a21a; border-radius: 10px; padding: 20px; margin: 24px 0;">
    <strong style="color: #795400;">Important</strong>
    <p style="margin: 7px 0 0 0; line-height: 1.75;">
        Processing times, limits, fees, withdrawal methods and other financial conditions are subject to the current rules displayed on the platform.
    </p>
</div>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">5. Security and Trust</h2>

<p style="line-height: 1.85;">
    Trust is one of the foundations of any marketplace. The platform uses operational mechanisms intended to improve account security, information quality and transaction visibility.
</p>

<ul style="line-height: 2;">
<li><strong>Information verification:</strong> required registration and operational information should be provided accurately.</li>
<li><strong>Access control:</strong> account credentials and access information should be protected by the user.</li>
<li><strong>Order monitoring:</strong> relevant operational stages remain recorded in the system.</li>
<li><strong>Operational monitoring:</strong> unusual activity may be reviewed according to applicable rules.</li>
<li><strong>Reputation:</strong> service quality and customer experience contribute to building trust.</li>
</ul>

<h2 style="border-bottom: 3px solid #0f5c46; padding-bottom: 9px; color: #102a43;">6. Built for Growth</h2>

<p style="line-height: 1.85;">
    New sellers need simplicity. Growing stores need organization. Larger operations need visibility and control. A modern marketplace infrastructure should support different stages of business development.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
    <div style="flex: 1; min-width: 210px; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
        <h3 style="color: #0f5c46; margin-top: 0;">Start</h3>
        <p style="line-height: 1.7; margin-bottom: 0;">Set up the store, publish products and understand the platform.</p>
    </div>
    <div style="flex: 1; min-width: 210px; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
        <h3 style="color: #0f5c46; margin-top: 0;">Operate</h3>
        <p style="line-height: 1.7; margin-bottom: 0;">Manage orders, inventory, customer service and financial information consistently.</p>
    </div>
    <div style="flex: 1; min-width: 210px; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
        <h3 style="color: #0f5c46; margin-top: 0;">Expand</h3>
        <p style="line-height: 1.7; margin-bottom: 0;">Grow the catalog, improve customer experience and develop sustainable operations.</p>
    </div>
</div>

<div style="background: #eef7f3; border-radius: 16px; padding: 26px; margin-top: 30px;">
<h3 style="color: #0f5c46; margin-top: 0;">Frequently Asked Questions</h3>
<p><strong>Do I need previous experience to start?</strong><br>Not necessarily. The key is to follow the platform guidelines, keep information accurate and operate the store responsibly.</p>
<p><strong>Where can I monitor my orders?</strong><br>Orders and their available statuses are displayed in the store dashboard.</p>
<p><strong>How can I monitor financial activity?</strong><br>The financial center provides available records related to sales, balances and financial operations.</p>
<p style="margin-bottom: 0;"><strong>What contributes most to store growth?</strong><br>Relevant products, strong images, complete descriptions, accurate inventory, competitive pricing, fast service and operational consistency are important factors.</p>
</div>

<div style="background: linear-gradient(135deg, #0f5c46 0%, #1f8a68 100%); border-radius: 18px; padding: 34px; margin-top: 32px; text-align: center; color: #ffffff;">
<h3 style="color: #ffffff; border: none; margin: 0 0 10px 0;">Build Your Next Stage</h3>
<p style="margin: 0; color: #dcefe8; line-height: 1.7;">
    A strong operation starts with simple processes, clear information and continuous attention to customer experience.
</p>
</div>
',
NULL, NOW(), NOW());


-- ============================================================
-- 白皮书 2：卖家指南 - 葡萄牙语
-- ============================================================

INSERT INTO `whitepaper_translations`
(`whitepaper_id`, `lang`, `title`, `summary`, `content`, `pdf`, `created_at`, `updated_at`)
VALUES
(1002, 'pt',
'Guia Completo do Vendedor',
'Um guia prático e completo para quem deseja começar a vender, configurar uma loja profissional, publicar produtos, administrar pedidos, atender clientes, acompanhar o desempenho e desenvolver uma operação sustentável.',
'
<div style="background: linear-gradient(135deg, #123a2a 0%, #1f7a52 60%, #2e9c6b 100%); border-radius: 20px; padding: 46px 34px; margin-bottom: 34px; color: #ffffff; text-align: center;">
    <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #c5e4d8; margin-bottom: 12px;">SELLER PLAYBOOK • BRASIL</div>
    <h2 style="color: #ffffff; border: none; margin: 0 0 16px 0; font-size: 30px; line-height: 1.3;">Venda com Estrutura. Cresça com Consistência.</h2>
    <p style="color: #e2f2ec; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 760px;">
        Um roteiro completo para transformar sua loja em uma operação organizada, confiável e preparada para crescer.
    </p>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 30px;">
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">01</div>
        <div style="font-weight: 700; color: #123a2a;">Configure</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Sua loja</div>
    </div>
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">02</div>
        <div style="font-weight: 700; color: #123a2a;">Publique</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Seus produtos</div>
    </div>
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">03</div>
        <div style="font-weight: 700; color: #123a2a;">Opere</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Seus pedidos</div>
    </div>
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">04</div>
        <div style="font-weight: 700; color: #123a2a;">Cresça</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Sua operação</div>
    </div>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">1. Antes de Começar</h2>

<p style="line-height: 1.85;">
    Uma loja profissional começa antes do primeiro pedido. Quanto melhor estiver preparada a estrutura inicial, mais simples será administrar a operação posteriormente.
</p>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 20px;">
<h3 style="color: #1f7a52; margin-top: 0;">Informações da conta</h3>
<ul style="line-height: 1.9; margin-bottom: 0;">
<li>Utilize informações verdadeiras e atualizadas.</li>
<li>Mantenha seus dados de contato acessíveis.</li>
<li>Proteja suas credenciais de acesso.</li>
<li>Complete as informações solicitadas pela plataforma.</li>
</ul>
</div>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 28px;">
<h3 style="color: #1f7a52; margin-top: 0;">Identidade da loja</h3>
<ul style="line-height: 1.9; margin-bottom: 0;">
<li>Escolha um nome fácil de memorizar.</li>
<li>Use uma descrição objetiva e profissional.</li>
<li>Explique claramente o tipo de produtos oferecidos.</li>
<li>Utilize uma identidade visual consistente.</li>
</ul>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">2. Cadastro de Produtos</h2>

<p style="line-height: 1.85;">
    O cadastro do produto é uma das etapas que mais influencia a primeira impressão do consumidor. Uma página de produto precisa responder rapidamente às principais dúvidas do cliente.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #1f7a52; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Elemento</th>
<th style="padding: 13px; text-align: left;">Boa prática</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Título</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Seja claro, específico e fácil de pesquisar.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Fotos</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Use imagens nítidas, bem iluminadas e de diferentes ângulos.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Descrição</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Explique características, benefícios, dimensões e informações relevantes.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Variações</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Configure corretamente tamanhos, cores ou modelos disponíveis.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Estoque</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Mantenha as quantidades atualizadas.</td>
</tr>
</tbody>
</table>

<div style="background: #fff8e8; border-left: 5px solid #e4a21a; border-radius: 10px; padding: 20px;">
<strong style="color: #795400;">Dica de conversão</strong>
<p style="margin: 7px 0 0 0; line-height: 1.75;">
    Antes de publicar, imagine que o cliente não poderá falar diretamente com você. A página do produto deve conter informação suficiente para reduzir dúvidas e facilitar a decisão de compra.
</p>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">3. Gestão de Pedidos</h2>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 24px;">

<div style="display: flex; margin-bottom: 20px;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">1</div>
<div><strong>Receba</strong><p style="margin: 4px 0 0; line-height: 1.7;">Verifique novos pedidos regularmente e confira os detalhes da compra.</p></div>
</div>

<div style="display: flex; margin-bottom: 20px;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">2</div>
<div><strong>Confirme</strong><p style="margin: 4px 0 0; line-height: 1.7;">Certifique-se de que produto, estoque e demais informações estão corretos.</p></div>
</div>

<div style="display: flex; margin-bottom: 20px;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">3</div>
<div><strong>Prepare</strong><p style="margin: 4px 0 0; line-height: 1.7;">Prepare o produto conforme as condições e os procedimentos de envio aplicáveis.</p></div>
</div>

<div style="display: flex;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">4</div>
<div><strong>Acompanhe</strong><p style="margin: 4px 0 0; line-height: 1.7;">Monitore o status até a conclusão da entrega.</p></div>
</div>

</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">4. Atendimento ao Cliente</h2>

<p style="line-height: 1.85;">
    Um bom atendimento não significa apenas responder rapidamente. Significa fornecer uma resposta clara, respeitosa e útil, reduzindo a incerteza do cliente durante toda a jornada.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
<div style="flex: 1; min-width: 200px; background: #eef7f3; border-radius: 14px; padding: 22px;">
<strong style="color: #1f7a52;">Responda</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Evite deixar perguntas sem resposta por longos períodos.</p>
</div>
<div style="flex: 1; min-width: 200px; background: #eef7f3; border-radius: 14px; padding: 22px;">
<strong style="color: #1f7a52;">Explique</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Use linguagem simples e informações objetivas.</p>
</div>
<div style="flex: 1; min-width: 200px; background: #eef7f3; border-radius: 14px; padding: 22px;">
<strong style="color: #1f7a52;">Resolva</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Priorize soluções em vez de respostas genéricas.</p>
</div>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">5. Gestão Financeira e Saques</h2>

<p style="line-height: 1.85;">
    O vendedor deve acompanhar regularmente seu centro financeiro e compreender a diferença entre valores de vendas, valores disponíveis e operações que ainda estão em processamento.
</p>

<ul style="line-height: 2;">
<li>Verifique o saldo disponível antes de solicitar uma retirada.</li>
<li>Confirme se os dados de recebimento estão corretos.</li>
<li>Acompanhe o status de cada solicitação.</li>
<li>Mantenha registros das movimentações relevantes.</li>
<li>Consulte as regras atuais da plataforma para prazos e condições.</li>
</ul>

<div style="background: #f1f7f4; border-radius: 14px; padding: 22px; margin-top: 20px;">
<h3 style="color: #1f7a52; margin-top: 0;">Métodos disponíveis</h3>
<table style="width: 100%; border-collapse: collapse;">
<tr>
<td style="padding: 12px; border-bottom: 1px solid #d7e5df;"><strong>Carteira digital</strong></td>
<td style="padding: 12px; border-bottom: 1px solid #d7e5df;">Utilize os métodos e redes suportados pela plataforma.</td>
</tr>
<tr>
<td style="padding: 12px;"><strong>Conta bancária</strong></td>
<td style="padding: 12px;">Utilize os dados bancários aceitos no sistema.</td>
</tr>
</table>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">6. Como Aumentar a Performance da Loja</h2>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #123a2a; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Ação</th>
<th style="padding: 13px; text-align: left;">Por que importa</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Melhorar fotos</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Aumenta a clareza visual do produto.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;">Revisar títulos</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Facilita a compreensão e a descoberta do produto.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Manter estoque</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Evita vendas de produtos indisponíveis.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;">Responder clientes</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Reduz dúvidas e melhora a experiência.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Analisar pedidos</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Ajuda a identificar produtos e padrões de demanda.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">7. Checklist Diário do Vendedor</h2>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 25px;">
<ul style="line-height: 2.1; margin-bottom: 0;">
<li>Verificar novos pedidos.</li>
<li>Conferir estoque dos principais produtos.</li>
<li>Responder mensagens dos clientes.</li>
<li>Verificar pedidos em processamento.</li>
<li>Acompanhar entregas.</li>
<li>Revisar saldo e movimentações financeiras.</li>
<li>Identificar produtos com baixo desempenho.</li>
<li>Atualizar informações quando necessário.</li>
</ul>
</div>

<div style="background: #fdeeee; border-left: 5px solid #c0392b; border-radius: 10px; padding: 20px; margin-top: 28px;">
<strong style="color: #a52a20;">Erros que devem ser evitados</strong>
<ul style="margin-bottom: 0; line-height: 1.9;">
<li>Publicar produtos sem informações suficientes.</li>
<li>Utilizar imagens de baixa qualidade.</li>
<li>Ignorar mensagens de clientes.</li>
<li>Manter estoque desatualizado.</li>
<li>Não acompanhar pedidos após a venda.</li>
<li>Compartilhar dados de acesso da conta.</li>
</ul>
</div>

<div style="background: linear-gradient(135deg, #1f7a52 0%, #2e9c6b 100%); border-radius: 18px; padding: 34px; margin-top: 32px; text-align: center; color: #ffffff;">
<h3 style="color: #ffffff; border: none; margin: 0 0 10px 0;">Sua Loja Pode Evoluir Todos os Dias</h3>
<p style="margin: 0; color: #e0f1e9; line-height: 1.7;">
    Crescimento sustentável não depende de uma única ação. Ele é construído através de bons produtos, atendimento consistente e melhoria contínua da operação.
</p>
</div>
',
NULL, NOW(), NOW());


-- ============================================================
-- 白皮书 2：卖家指南 - English
-- ============================================================

INSERT INTO `whitepaper_translations`
(`whitepaper_id`, `lang`, `title`, `summary`, `content`, `pdf`, `created_at`, `updated_at`)
VALUES
(1002, 'en',
'Complete Seller Guide',
'A practical guide for sellers who want to start, configure a professional store, publish products, manage orders, serve customers, monitor performance and build a sustainable operation.',
'
<div style="background: linear-gradient(135deg, #123a2a 0%, #1f7a52 60%, #2e9c6b 100%); border-radius: 20px; padding: 46px 34px; margin-bottom: 34px; color: #ffffff; text-align: center;">
    <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #c5e4d8; margin-bottom: 12px;">SELLER PLAYBOOK • BRAZIL</div>
    <h2 style="color: #ffffff; border: none; margin: 0 0 16px 0; font-size: 30px; line-height: 1.3;">Sell with Structure. Grow with Consistency.</h2>
    <p style="color: #e2f2ec; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 760px;">
        A complete roadmap for turning your store into an organized, trusted and growth-ready operation.
    </p>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 30px;">
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">01</div>
        <div style="font-weight: 700; color: #123a2a;">Configure</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Your store</div>
    </div>
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">02</div>
        <div style="font-weight: 700; color: #123a2a;">Publish</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Your products</div>
    </div>
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">03</div>
        <div style="font-weight: 700; color: #123a2a;">Operate</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Your orders</div>
    </div>
    <div style="flex: 1; min-width: 160px; background: #eef7f3; border-radius: 14px; padding: 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: 700; color: #1f7a52;">04</div>
        <div style="font-weight: 700; color: #123a2a;">Grow</div>
        <div style="font-size: 13px; color: #63746d; margin-top: 5px;">Your operation</div>
    </div>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">1. Before You Start</h2>

<p style="line-height: 1.85;">
    A professional store starts before the first order. The better the initial setup, the easier it becomes to manage the operation later.
</p>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 20px;">
<h3 style="color: #1f7a52; margin-top: 0;">Account Information</h3>
<ul style="line-height: 1.9; margin-bottom: 0;">
<li>Use accurate and up-to-date information.</li>
<li>Keep your contact information accessible.</li>
<li>Protect your account credentials.</li>
<li>Complete all information requested by the platform.</li>
</ul>
</div>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 28px;">
<h3 style="color: #1f7a52; margin-top: 0;">Store Identity</h3>
<ul style="line-height: 1.9; margin-bottom: 0;">
<li>Choose a name that is easy to remember.</li>
<li>Write a clear and professional store description.</li>
<li>Explain what type of products you offer.</li>
<li>Maintain a consistent visual identity.</li>
</ul>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">2. Product Listing</h2>

<p style="line-height: 1.85;">
    A product listing strongly influences the customer''s first impression. A good product page should quickly answer the most important questions a customer may have.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #1f7a52; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Element</th>
<th style="padding: 13px; text-align: left;">Best Practice</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Title</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Be clear, specific and easy to search.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Photos</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Use sharp, well-lit images from different angles.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Description</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Explain features, benefits, dimensions and relevant details.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Variations</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Correctly configure available sizes, colors or models.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Inventory</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Keep quantities up to date.</td>
</tr>
</tbody>
</table>

<div style="background: #fff8e8; border-left: 5px solid #e4a21a; border-radius: 10px; padding: 20px;">
<strong style="color: #795400;">Conversion Tip</strong>
<p style="margin: 7px 0 0 0; line-height: 1.75;">
    Imagine the customer cannot contact you directly. Your product page should contain enough information to reduce uncertainty and make the purchase decision easier.
</p>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">3. Order Management</h2>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 26px; margin-bottom: 24px;">

<div style="display: flex; margin-bottom: 20px;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">1</div>
<div><strong>Receive</strong><p style="margin: 4px 0 0; line-height: 1.7;">Check new orders regularly and review the purchase details.</p></div>
</div>

<div style="display: flex; margin-bottom: 20px;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">2</div>
<div><strong>Confirm</strong><p style="margin: 4px 0 0; line-height: 1.7;">Make sure the product, inventory and other information are correct.</p></div>
</div>

<div style="display: flex; margin-bottom: 20px;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">3</div>
<div><strong>Prepare</strong><p style="margin: 4px 0 0; line-height: 1.7;">Prepare the product according to applicable shipping conditions and procedures.</p></div>
</div>

<div style="display: flex;">
<div style="width: 36px; height: 36px; background: #1f7a52; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 14px; flex-shrink: 0;">4</div>
<div><strong>Track</strong><p style="margin: 4px 0 0; line-height: 1.7;">Monitor the order status until delivery is completed.</p></div>
</div>

</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">4. Customer Service</h2>

<p style="line-height: 1.85;">
    Good customer service is not only about speed. It means providing clear, respectful and useful answers that reduce uncertainty throughout the customer''s journey.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
<div style="flex: 1; min-width: 200px; background: #eef7f3; border-radius: 14px; padding: 22px;">
<strong style="color: #1f7a52;">Respond</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Avoid leaving customer questions unanswered for long periods.</p>
</div>
<div style="flex: 1; min-width: 200px; background: #eef7f3; border-radius: 14px; padding: 22px;">
<strong style="color: #1f7a52;">Explain</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Use simple language and objective information.</p>
</div>
<div style="flex: 1; min-width: 200px; background: #eef7f3; border-radius: 14px; padding: 22px;">
<strong style="color: #1f7a52;">Resolve</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Prioritize solutions instead of generic responses.</p>
</div>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">5. Financial Management and Withdrawals</h2>

<p style="line-height: 1.85;">
    Sellers should regularly review the financial center and understand the difference between sales values, available balances and transactions that are still being processed.
</p>

<ul style="line-height: 2;">
<li>Check your available balance before requesting a withdrawal.</li>
<li>Confirm that your payment details are correct.</li>
<li>Monitor the status of each request.</li>
<li>Keep records of relevant transactions.</li>
<li>Review the platform''s current rules for processing times and conditions.</li>
</ul>

<div style="background: #f1f7f4; border-radius: 14px; padding: 22px; margin-top: 20px;">
<h3 style="color: #1f7a52; margin-top: 0;">Available Methods</h3>
<table style="width: 100%; border-collapse: collapse;">
<tr>
<td style="padding: 12px; border-bottom: 1px solid #d7e5df;"><strong>Digital wallet</strong></td>
<td style="padding: 12px; border-bottom: 1px solid #d7e5df;">Use the methods and networks supported by the platform.</td>
</tr>
<tr>
<td style="padding: 12px;"><strong>Bank account</strong></td>
<td style="padding: 12px;">Use the banking details accepted by the system.</td>
</tr>
</table>
</div>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">6. How to Improve Store Performance</h2>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #123a2a; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Action</th>
<th style="padding: 13px; text-align: left;">Why It Matters</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Improve product photos</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Makes the product easier to understand visually.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;">Review product titles</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Improves clarity and product discovery.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Maintain inventory</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Prevents orders for unavailable products.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;">Reply to customers</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Reduces uncertainty and improves the customer experience.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Analyze orders</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Helps identify products and demand patterns.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #1f7a52; padding-bottom: 9px; color: #123a2a;">7. Seller Daily Checklist</h2>

<div style="background: #f7faf9; border: 1px solid #dce9e4; border-radius: 16px; padding: 25px;">
<ul style="line-height: 2.1; margin-bottom: 0;">
<li>Check new orders.</li>
<li>Review inventory for key products.</li>
<li>Respond to customer messages.</li>
<li>Monitor orders in progress.</li>
<li>Track deliveries.</li>
<li>Review balance and financial activity.</li>
<li>Identify underperforming products.</li>
<li>Update information when necessary.</li>
</ul>
</div>

<div style="background: #fdeeee; border-left: 5px solid #c0392b; border-radius: 10px; padding: 20px; margin-top: 28px;">
<strong style="color: #a52a20;">Common Mistakes to Avoid</strong>
<ul style="margin-bottom: 0; line-height: 1.9;">
<li>Publishing products without sufficient information.</li>
<li>Using low-quality images.</li>
<li>Ignoring customer messages.</li>
<li>Keeping inventory information outdated.</li>
<li>Failing to monitor orders after purchase.</li>
<li>Sharing account access information.</li>
</ul>
</div>

<div style="background: linear-gradient(135deg, #1f7a52 0%, #2e9c6b 100%); border-radius: 18px; padding: 34px; margin-top: 32px; text-align: center; color: #ffffff;">
<h3 style="color: #ffffff; border: none; margin: 0 0 10px 0;">Your Store Can Improve Every Day</h3>
<p style="margin: 0; color: #e0f1e9; line-height: 1.7;">
    Sustainable growth rarely comes from one action. It is built through strong products, consistent service and continuous operational improvement.
</p>
</div>
',
NULL, NOW(), NOW());


-- ============================================================
-- 白皮书 3：巴西电商市场报告 - 葡萄牙语
-- ============================================================

INSERT INTO `whitepaper_translations`
(`whitepaper_id`, `lang`, `title`, `summary`, `content`, `pdf`, `created_at`, `updated_at`)
VALUES
(1003, 'pt',
'Panorama do Mercado de E-commerce no Brasil',
'Uma visão estratégica sobre o comércio eletrônico brasileiro, incluindo comportamento do consumidor, pagamentos digitais, mobile commerce, logística, categorias de oportunidade, competitividade e tendências que devem moldar os próximos anos.',
'
<div style="background: linear-gradient(135deg, #101c33 0%, #144d3f 55%, #1f7a5c 100%); border-radius: 20px; padding: 46px 34px; margin-bottom: 34px; color: #ffffff; text-align: center;">
    <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #c7ddd7; margin-bottom: 12px;">MARKET INSIGHTS • BRAZIL</div>
    <h2 style="color: #ffffff; border: none; margin: 0 0 16px 0; font-size: 30px; line-height: 1.3;">O Mercado Digital Brasileiro em Transformação</h2>
    <p style="color: #dcece7; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 780px;">
        Um panorama estratégico para compreender o comportamento dos consumidores e as oportunidades de crescimento dentro do maior mercado de comércio eletrônico da América Latina.
    </p>
</div>

<div style="background: #f5faf8; border: 1px solid #dcebe5; border-radius: 16px; padding: 24px; margin-bottom: 30px;">
<h3 style="color: #144d3f; margin-top: 0;">Por que o Brasil importa?</h3>
<p style="line-height: 1.8; margin-bottom: 0;">
    O Brasil reúne uma grande base de consumidores, forte utilização de dispositivos móveis, rápida evolução dos pagamentos digitais e uma enorme diversidade regional. Para vendedores preparados, essa combinação cria oportunidades em diferentes categorias e modelos de negócio.
</p>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 32px;">
<div style="flex: 1; min-width: 180px; background: #eef6f1; border-radius: 14px; padding: 22px; text-align: center; border: 1px solid #d8e9e1;">
<div style="font-size: 25px; font-weight: 700; color: #144d3f;">Mobile</div>
<div style="font-size: 13px; color: #60726b; margin-top: 6px;">Experiência de compra cada vez mais centrada no smartphone</div>
</div>
<div style="flex: 1; min-width: 180px; background: #eef6f1; border-radius: 14px; padding: 22px; text-align: center; border: 1px solid #d8e9e1;">
<div style="font-size: 25px; font-weight: 700; color: #144d3f;">PIX</div>
<div style="font-size: 13px; color: #60726b; margin-top: 6px;">Um dos elementos centrais do ecossistema de pagamentos</div>
</div>
<div style="flex: 1; min-width: 180px; background: #eef6f1; border-radius: 14px; padding: 22px; text-align: center; border: 1px solid #d8e9e1;">
<div style="font-size: 25px; font-weight: 700; color: #144d3f;">Escala</div>
<div style="font-size: 13px; color: #60726b; margin-top: 6px;">Mercado amplo e altamente diversificado regionalmente</div>
</div>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">1. Características do Consumidor Brasileiro</h2>

<p style="line-height: 1.85;">
    O consumidor brasileiro não é homogêneo. Diferenças de renda, localização, hábitos culturais e infraestrutura fazem com que diferentes regiões apresentem necessidades distintas.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #144d3f; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Fator</th>
<th style="padding: 13px; text-align: left;">Implicação para o vendedor</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Preço</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Competitividade e percepção de valor são importantes.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Confiança</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Avaliações, reputação e informações claras reduzem incerteza.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Conveniência</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Processos simples ajudam a reduzir abandono.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Velocidade</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Informações de entrega e acompanhamento são cada vez mais relevantes.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Atendimento</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Respostas claras contribuem para uma experiência positiva.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">2. A Revolução dos Pagamentos Digitais</h2>

<p style="line-height: 1.85;">
    Um dos principais diferenciais do mercado brasileiro é a diversidade e a evolução de seus meios de pagamento. O PIX transformou a experiência de pagamentos instantâneos, enquanto cartões continuam relevantes para diferentes perfis de consumidores.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
<div style="flex: 1; min-width: 210px; background: #ffffff; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
<h3 style="color: #144d3f; margin-top: 0;">PIX</h3>
<p style="line-height: 1.7; margin-bottom: 0;">Agilidade e praticidade fazem do PIX uma parte essencial da experiência digital brasileira.</p>
</div>
<div style="flex: 1; min-width: 210px; background: #ffffff; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
<h3 style="color: #144d3f; margin-top: 0;">Cartões</h3>
<p style="line-height: 1.7; margin-bottom: 0;">O parcelamento é relevante em diversas categorias e pode influenciar decisões de compra.</p>
</div>
<div style="flex: 1; min-width: 210px; background: #ffffff; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
<h3 style="color: #144d3f; margin-top: 0;">Carteiras Digitais</h3>
<p style="line-height: 1.7; margin-bottom: 0;">A integração de serviços financeiros digitais continua ampliando as opções para consumidores.</p>
</div>
</div>

<div style="background: #fff8e8; border-left: 5px solid #e4a21a; border-radius: 10px; padding: 20px;">
<strong style="color: #795400;">Insight</strong>
<p style="margin: 7px 0 0 0; line-height: 1.75;">
    Para o vendedor, oferecer uma experiência de pagamento simples e familiar é tão importante quanto apresentar um bom produto.
</p>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">3. Mobile Commerce</h2>

<p style="line-height: 1.85;">
    Smartphones ocupam uma posição central na jornada digital brasileira. O consumidor pesquisa produtos, compara preços, conversa com vendedores e acompanha pedidos diretamente pelo celular.
</p>

<div style="background: #eef6f1; border-radius: 16px; padding: 25px; margin: 24px 0;">
<h3 style="color: #144d3f; margin-top: 0;">O que isso significa para uma loja?</h3>
<ul style="line-height: 2; margin-bottom: 0;">
<li>Fotos precisam funcionar bem em telas pequenas.</li>
<li>Títulos devem ser fáceis de compreender rapidamente.</li>
<li>Informações importantes devem aparecer sem excesso de texto.</li>
<li>O processo de compra precisa ter o mínimo possível de atrito.</li>
<li>O atendimento móvel deve ser rápido e objetivo.</li>
</ul>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">4. Logística: Um Desafio e uma Oportunidade</h2>

<p style="line-height: 1.85;">
    A dimensão continental do Brasil torna a logística um dos principais elementos competitivos do comércio eletrônico. Distâncias, infraestrutura, densidade populacional e características regionais podem afetar o custo e o prazo de entrega.
</p>

<p style="line-height: 1.85;">
    Por isso, a experiência de entrega deve ser tratada como parte do produto. Informações claras, acompanhamento do pedido e comunicação adequada ajudam a reduzir a ansiedade do consumidor.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #101c33; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Desafio</th>
<th style="padding: 13px; text-align: left;">Boa resposta operacional</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Grandes distâncias</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Planejamento de estoque e logística.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;">Expectativa por velocidade</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Processos rápidos e comunicação transparente.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Rastreamento</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Disponibilizar informações de acompanhamento sempre que possível.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">5. Categorias com Potencial</h2>

<p style="line-height: 1.85;">
    O tamanho do mercado permite oportunidades em diversas categorias. O mais importante para o vendedor não é simplesmente escolher a categoria mais popular, mas encontrar uma combinação adequada entre demanda, margem, concorrência e capacidade operacional.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #144d3f; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Categoria</th>
<th style="padding: 13px; text-align: left;">Oportunidade</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Moda e acessórios</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Grande variedade de produtos e forte presença digital.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Beleza e cuidados pessoais</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Potencial de recompra e forte presença de marcas e nichos.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Casa e decoração</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Grande diversidade de necessidades e estilos de consumo.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Eletrônicos e acessórios</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Demanda constante por tecnologia e acessórios complementares.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Pet</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Mercado com diferentes necessidades e possibilidade de recorrência.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">6. Competição e Diferenciação</h2>

<p style="line-height: 1.85;">
    À medida que o comércio eletrônico amadurece, competir apenas pelo menor preço torna-se cada vez mais difícil. Lojas bem posicionadas procuram construir diferenciais que o cliente consiga perceber.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Curadoria</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Escolha produtos coerentes com um público específico.</p>
</div>
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Apresentação</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Invista em fotos, descrições e identidade visual.</p>
</div>
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Serviço</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Transforme atendimento em uma vantagem competitiva.</p>
</div>
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Consistência</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Mantenha qualidade em todas as etapas da operação.</p>
</div>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">7. Tendências que Merecem Atenção</h2>

<ul style="line-height: 2;">
<li>Maior integração entre canais digitais e físicos.</li>
<li>Experiências de compra cada vez mais orientadas por dispositivos móveis.</li>
<li>Maior utilização de pagamentos instantâneos e digitais.</li>
<li>Consumidores mais atentos a avaliações e reputação.</li>
<li>Maior expectativa por transparência logística.</li>
<li>Personalização de ofertas e comunicação.</li>
<li>Profissionalização crescente de pequenos e médios vendedores.</li>
<li>Uso crescente de automação para atendimento, marketing e operações.</li>
</ul>

<div style="background: #eef6f1; border-radius: 16px; padding: 26px; margin-top: 28px;">
<h3 style="color: #144d3f; margin-top: 0;">Conclusão</h3>
<p style="line-height: 1.8;">
    O mercado brasileiro oferece um ambiente amplo e diversificado para o comércio eletrônico. Entretanto, tamanho de mercado por si só não garante sucesso. Os vendedores que melhor combinam seleção de produtos, preço, apresentação, atendimento, logística e gestão tendem a estar mais preparados para construir operações duradouras.
</p>
<p style="line-height: 1.8; margin-bottom: 0;">
    Para uma nova geração de vendedores, a oportunidade está em tratar o e-commerce como um negócio completo — e não apenas como um canal de vendas.
</p>
</div>

<div style="background: linear-gradient(135deg, #144d3f 0%, #1f7a5c 100%); border-radius: 18px; padding: 34px; margin-top: 32px; text-align: center; color: #ffffff;">
<h3 style="color: #ffffff; border: none; margin: 0 0 10px 0;">O Próximo Capítulo do E-commerce é Digital</h3>
<p style="margin: 0; color: #dcefe8; line-height: 1.7;">
    Prepare sua operação hoje para acompanhar a evolução do consumidor brasileiro amanhã.
</p>
</div>
',
NULL, NOW(), NOW());


-- ============================================================
-- 白皮书 3：巴西电商市场报告 - English
-- ============================================================

INSERT INTO `whitepaper_translations`
(`whitepaper_id`, `lang`, `title`, `summary`, `content`, `pdf`, `created_at`, `updated_at`)
VALUES
(1003, 'en',
'Brazil E-commerce Market Outlook',
'A strategic overview of Brazilian e-commerce, covering consumer behavior, digital payments, mobile commerce, logistics, category opportunities, competition and the trends shaping the next stage of digital retail.',
'
<div style="background: linear-gradient(135deg, #101c33 0%, #144d3f 55%, #1f7a5c 100%); border-radius: 20px; padding: 46px 34px; margin-bottom: 34px; color: #ffffff; text-align: center;">
    <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #c7ddd7; margin-bottom: 12px;">MARKET INSIGHTS • BRAZIL</div>
    <h2 style="color: #ffffff; border: none; margin: 0 0 16px 0; font-size: 30px; line-height: 1.3;">Brazil's Digital Market in Transformation</h2>
    <p style="color: #dcece7; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 780px;">
        A strategic overview of consumer behavior and growth opportunities within Latin America's largest e-commerce market.
    </p>
</div>

<div style="background: #f5faf8; border: 1px solid #dcebe5; border-radius: 16px; padding: 24px; margin-bottom: 30px;">
<h3 style="color: #144d3f; margin-top: 0;">Why Brazil Matters</h3>
<p style="line-height: 1.8; margin-bottom: 0;">
    Brazil combines a large consumer base, extensive mobile usage, rapidly evolving digital payments and significant regional diversity. For prepared sellers, this creates opportunities across categories and business models.
</p>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 32px;">
<div style="flex: 1; min-width: 180px; background: #eef6f1; border-radius: 14px; padding: 22px; text-align: center; border: 1px solid #d8e9e1;">
<div style="font-size: 25px; font-weight: 700; color: #144d3f;">Mobile</div>
<div style="font-size: 13px; color: #60726b; margin-top: 6px;">Shopping experiences increasingly centered on smartphones</div>
</div>
<div style="flex: 1; min-width: 180px; background: #eef6f1; border-radius: 14px; padding: 22px; text-align: center; border: 1px solid #d8e9e1;">
<div style="font-size: 25px; font-weight: 700; color: #144d3f;">PIX</div>
<div style="font-size: 13px; color: #60726b; margin-top: 6px;">A central element of Brazil's digital payment ecosystem</div>
</div>
<div style="flex: 1; min-width: 180px; background: #eef6f1; border-radius: 14px; padding: 22px; text-align: center; border: 1px solid #d8e9e1;">
<div style="font-size: 25px; font-weight: 700; color: #144d3f;">Scale</div>
<div style="font-size: 13px; color: #60726b; margin-top: 6px;">A broad and highly diverse regional market</div>
</div>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">1. Understanding Brazilian Consumers</h2>

<p style="line-height: 1.85;">
    Brazilian consumers are not a single homogeneous group. Differences in income, geography, culture and infrastructure mean that different regions can have distinct purchasing needs and behaviors.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #144d3f; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Factor</th>
<th style="padding: 13px; text-align: left;">Implication for Sellers</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Price</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Competitiveness and perceived value matter.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Trust</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Reviews, reputation and clear information reduce uncertainty.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Convenience</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Simple processes can help reduce purchase friction.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Speed</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Delivery information and tracking are increasingly important.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Service</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Clear communication contributes to a positive experience.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">2. The Digital Payment Revolution</h2>

<p style="line-height: 1.85;">
    One of the defining characteristics of Brazil's digital economy is the diversity and evolution of its payment infrastructure. PIX has transformed instant payments, while cards remain highly relevant across many consumer segments.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
<div style="flex: 1; min-width: 210px; background: #ffffff; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
<h3 style="color: #144d3f; margin-top: 0;">PIX</h3>
<p style="line-height: 1.7; margin-bottom: 0;">Speed and convenience have made PIX an essential part of the Brazilian digital payment experience.</p>
</div>
<div style="flex: 1; min-width: 210px; background: #ffffff; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
<h3 style="color: #144d3f; margin-top: 0;">Cards</h3>
<p style="line-height: 1.7; margin-bottom: 0;">Installment payments remain important across many product categories and can influence purchase decisions.</p>
</div>
<div style="flex: 1; min-width: 210px; background: #ffffff; border: 1px solid #dce9e4; border-radius: 14px; padding: 22px;">
<h3 style="color: #144d3f; margin-top: 0;">Digital Wallets</h3>
<p style="line-height: 1.7; margin-bottom: 0;">Digital financial services continue to expand the options available to consumers.</p>
</div>
</div>

<div style="background: #fff8e8; border-left: 5px solid #e4a21a; border-radius: 10px; padding: 20px;">
<strong style="color: #795400;">Insight</strong>
<p style="margin: 7px 0 0 0; line-height: 1.75;">
    For sellers, providing a familiar and convenient payment experience can be just as important as offering a strong product.
</p>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">3. Mobile Commerce</h2>

<p style="line-height: 1.85;">
    Smartphones play a central role in Brazil's digital customer journey. Consumers research products, compare prices, communicate with sellers and monitor orders directly from their phones.
</p>

<div style="background: #eef6f1; border-radius: 16px; padding: 25px; margin: 24px 0;">
<h3 style="color: #144d3f; margin-top: 0;">What Does This Mean for a Store?</h3>
<ul style="line-height: 2; margin-bottom: 0;">
<li>Product images should work well on smaller screens.</li>
<li>Titles should be easy to understand at a glance.</li>
<li>Important information should be visible without excessive text.</li>
<li>The checkout journey should have as little friction as possible.</li>
<li>Mobile customer service should be fast and concise.</li>
</ul>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">4. Logistics: A Challenge and an Opportunity</h2>

<p style="line-height: 1.85;">
    Brazil's continental size makes logistics one of the most important competitive factors in e-commerce. Distance, infrastructure, population density and regional characteristics can affect delivery costs and timelines.
</p>

<p style="line-height: 1.85;">
    For this reason, delivery should be treated as part of the product experience. Clear information, order tracking and appropriate communication can reduce customer uncertainty.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #101c33; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Challenge</th>
<th style="padding: 13px; text-align: left;">Operational Response</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Long distances</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Inventory and logistics planning.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;">Demand for speed</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Efficient processes and transparent communication.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;">Tracking</td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Provide order tracking information whenever available.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">5. Categories with Potential</h2>

<p style="line-height: 1.85;">
    The scale of the market creates opportunities across many categories. For sellers, the most important factor is not simply choosing the most popular category, but finding the right balance between demand, margin, competition and operational capability.
</p>

<table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
<thead>
<tr style="background: #144d3f; color: #ffffff;">
<th style="padding: 13px; text-align: left;">Category</th>
<th style="padding: 13px; text-align: left;">Opportunity</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Fashion and Accessories</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Broad product variety and strong digital presence.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Beauty and Personal Care</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Potential for repeat purchases and strong niche development.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Home and Decor</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Wide range of consumer needs and styles.</td>
</tr>
<tr style="background: #f3f8f5;">
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Electronics and Accessories</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Consistent interest in technology and complementary accessories.</td>
</tr>
<tr>
<td style="padding: 13px; border: 1px solid #d5e8de;"><strong>Pet</strong></td>
<td style="padding: 13px; border: 1px solid #d5e8de;">Diverse needs and opportunities for recurring purchases.</td>
</tr>
</tbody>
</table>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">6. Competition and Differentiation</h2>

<p style="line-height: 1.85;">
    As e-commerce becomes more mature, competing only on the lowest price becomes increasingly difficult. Strong stores build differentiators that customers can actually recognize.
</p>

<div style="display: flex; flex-wrap: wrap; gap: 14px; margin: 24px 0;">
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Curation</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Choose products that fit a clearly defined audience.</p>
</div>
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Presentation</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Invest in product photography, descriptions and visual identity.</p>
</div>
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Service</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Turn customer service into a competitive advantage.</p>
</div>
<div style="flex: 1; min-width: 190px; background: #f7faf9; border: 1px solid #dce9e4; border-radius: 14px; padding: 21px;">
<strong style="color: #144d3f;">Consistency</strong>
<p style="line-height: 1.7; margin-bottom: 0;">Maintain quality across every stage of the operation.</p>
</div>
</div>

<h2 style="border-bottom: 3px solid #144d3f; padding-bottom: 9px; color: #101c33;">7. Trends to Watch</h2>

<ul style="line-height: 2;">
<li>Greater integration between digital and physical channels.</li>
<li>Shopping experiences increasingly centered on mobile devices.</li>
<li>Continued adoption of instant and digital payment methods.</li>
<li>Consumers paying greater attention to reviews and reputation.</li>
<li>Growing expectations around logistics transparency.</li>
<li>More personalized offers and communication.</li>
<li>Increasing professionalization of small and medium-sized sellers.</li>
<li>Growing use of automation for customer service, marketing and operations.</li>
</ul>

<div style="background: #eef6f1; border-radius: 16px; padding: 26px; margin-top: 28px;">
<h3 style="color: #144d3f; margin-top: 0;">Conclusion</h3>
<p style="line-height: 1.8;">
    Brazil offers a broad and diverse environment for e-commerce. However, market size alone does not guarantee success. Sellers who combine product selection, pricing, presentation, service, logistics and disciplined management are better positioned to build durable businesses.
</p>
<p style="line-height: 1.8; margin-bottom: 0;">
    For the next generation of sellers, the opportunity is to treat e-commerce as a complete business rather than simply another sales channel.
</p>
</div>

<div style="background: linear-gradient(135deg, #144d3f 0%, #1f7a5c 100%); border-radius: 18px; padding: 34px; margin-top: 32px; text-align: center; color: #ffffff;">
<h3 style="color: #ffffff; border: none; margin: 0 0 10px 0;">The Next Chapter of E-commerce is Digital</h3>
<p style="margin: 0; color: #dcefe8; line-height: 1.7;">
    Prepare your operation today to keep pace with tomorrow's Brazilian consumer.
</p>
</div>
',
NULL, NOW(), NOW());