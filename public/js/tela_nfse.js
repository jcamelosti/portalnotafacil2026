$(document).ready(function () {

    // ==========================================
    // CAMPOS TRIBUTÁRIOS FEDERAIS
    // ==========================================

    const $sitTribFederal   = $('#ddlSitTribFederal');
    const $tipoRetFederal   = $('#ddlTipoRetFederal');

    const $baseCalcFederal  = $('#txtBaseCalcFederal');
    const $aliqPIS          = $('#txtAliqPIS');
    const $aliqCOFINS       = $('#txtAliqCOFINS');

    const $valorPIS         = $('#txtValorPis');
    const $valorCOFINS      = $('#txtValorCOFINS');
    const $valorCSLL        = $('#txtValorCSLL');
    const $valorIRRF        = $('#txtValorIRRF');
    const $valorCP          = $('#txtValorCP');


    // ==========================================
    // CONTAINERS
    // ==========================================

    const $divBaseCalcFederal = $('#divBaseCalcFederal');
    const $divAliqPIS         = $('#divAliqPIS');
    const $divAliqCOFINS      = $('#divAliqCOFINS');

    const $divValorPIS        = $('#divValorPis');
    const $divValorCOFINS     = $('#divValorCOFINS');
    const $divValorCSLL       = $('#divValorCSLL');
    const $divValorIRRF       = $('#divValorIRRF');
    const $divValorCP         = $('#divValorCP');

    const cstsCredito = [
        '50', '51', '52', '53', '54', '55', '56',
        '60', '61', '62', '63', '64', '65', '66',
        '70', '71', '72', '73', '74', '75',
        '98', '99'
    ];


    // ==========================================
    // INICIALIZAÇÃO
    // ==========================================
    
    $tipoRetFederal.closest('label').hide();
    $divBaseCalcFederal.closest('label').hide()

    resetarTributacaoFederal();

    $tipoRetFederal.on('change', function () {
        const tpRetFederal = $(this).val();
        const situacaoTribPisCofins = $sitTribFederal.val();

        switch (tpRetFederal) {
            case '0':
                if(situacaoTribPisCofins == '00'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '01'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '08'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '09'){
                    desabilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '49'){
                    desabilitarCampo($valorCSLL);
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        desabilitarCampo($valorCSLL);
                    }
                } 
                break;

            case '1':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '2':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '3':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '4':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '5':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '6':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '7':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;

            case '8':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(cst)) {
                        habilitarCampo($valorCSLL) 
                    }
                }              
                break;

            case '9':
                if(situacaoTribPisCofins == '00'){
                    habilitarCampo($valorCSLL);
                    habilitarCampo($valorIRRF);
                    habilitarCampo($valorCP)
                }else if(situacaoTribPisCofins == '01'){
                    desabilitarCampo($valorPIS);
                    desabilitarCampo($valorCOFINS);
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '02'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '03'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '04'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '05'){
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '06'){
                    desabilitarCampo($aliqPIS);
                    $aliqPIS.val('0,00');
                    desabilitarCampo($aliqCOFINS);
                    $aliqCOFINS.val('0,00');
                    habilitarCampo($valorCSLL);
                }else if(situacaoTribPisCofins == '07'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '08'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '09'){
                    habilitarCampo($valorCSLL)
                }else if(situacaoTribPisCofins == '49'){
                    habilitarCampo($valorCSLL)
                }else{
                    if (cstsCredito.includes(situacaoTribPisCofins)) {
                        habilitarCampo($valorCSLL)
                    }
                } 
                break;
        }
    });

    $sitTribFederal.on('change', function () {
        const situacaoTribPisCofins = $(this).val();

        // Primeiro limpa o estado anterior
        resetarTributacaoFederal();

        $tipoRetFederal.closest('label').show();

        switch (situacaoTribPisCofins) {
            case '00':
                cst00();
                break;
            case '01':
                cst01();
                break;

            case '02':
                cst02();
                break;

            case '03':
                cst03();
                break;

            case '04':
                cst04();
                break;

            case '05':
                cst05();
                break;

            case '06':
                cst06();
                break;

            case '07':
                cst07();
                break;

            case '08':
                cst08();
                break;

            case '09':
                cst09();
                break;

            case '49':
                cst49();
                break;

            default:
                if (cstsCredito.includes(situacaoTribPisCofins)) {
                    configurarCSTCredito();
                }
                break;
        }
    });

    function mostrarCampo($campo) {
        $campo.closest('label').show();
    }

    function ocultarCampo($campo) {
        $campo.closest('label').hide();
    }

    function habilitarCampo($campo) {
        $campo.prop('disabled', false);
        $campo.css('background-color', '');
    }

    function desabilitarCampo($campo) {

        $campo.prop('disabled', true);
        $campo.css('background-color', '#D3D3D3');
    }

    function limparCampo($campo) {
        $campo.val('');
    }

    function configurarValoresCalculados() {
        desabilitarCampo($valorPIS);
        desabilitarCampo($valorCOFINS);
    }

    function cst00(){
        $divValorCSLL.show();
        habilitarCampo($valorCSLL);

        $divValorIRRF.show();
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);
    
        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst01() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        desabilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst02() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst03() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst04() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        desabilitarCampo($aliqPIS);
        $aliqPIS.val('0,00');

        mostrarCampo($aliqCOFINS);
        desabilitarCampo($aliqCOFINS);
        $aliqCOFINS.val('0,00');

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst05() {

        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst06() {
       $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        desabilitarCampo($aliqPIS);
        $aliqPIS.val('0,00');

        mostrarCampo($aliqCOFINS);
        desabilitarCampo($aliqCOFINS);
        $aliqCOFINS.val('0,00');

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        desabilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }    

    function cst07() {

       $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst08() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst09() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    function cst49() {
        $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        habilitarCampo($aliqPIS);

        mostrarCampo($aliqCOFINS);
        habilitarCampo($aliqCOFINS);

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }

    //se tiver particularidade depois chamo separado
    /*function cst50() {
        configurarCSTCredito();
    }

    function cst51() {
        configurarCSTCredito();
    }

    function cst52() {
        configurarCSTCredito();
    }

    function cst53() {
        configurarCSTCredito();
    }

    function cst54() {
        configurarCSTCredito();
    }

    function cst55() {
        configurarCSTCredito();
    }

    function cst56() {
        configurarCSTCredito();
    }

    function cst60() {
        configurarCSTCredito();
    }

    function cst61() {
        configurarCSTCredito();
    }

    function cst62() {
        configurarCSTCredito();
    }

    function cst63() {
        configurarCSTCredito();
    }

    function cst64() {
        configurarCSTCredito();
    }

    function cst65() {
        configurarCSTCredito();
    }

    function cst66() {
        configurarCSTCredito();
    }


    function cst70() {
        configurarCSTCredito();
    }

    function cst71() {
        configurarCSTCredito();
    }

    function cst72() {
        configurarCSTCredito();
    }

    function cst73() {
        configurarCSTCredito();
    }

    function cst74() {
        configurarCSTCredito();
    }

    function cst75() {
        configurarCSTCredito();
    }

    function cst98() {
        configurarCSTCredito();
    }

    function cst99() {
        configurarCSTCredito();
    }*/
    
    function resetarTributacaoFederal() {
        $baseCalcFederal.closest('label').hide();
        $aliqPIS.closest('label').hide();
        $aliqCOFINS.closest('label').hide();

        $tipoRetFederal.closest('label').hide();

        $valorPIS.closest('div').hide();
        $valorCOFINS.closest('div').hide();
        $valorCSLL.closest('div').hide();
        $valorIRRF.closest('div').hide();
        $valorCP.closest('div').hide();

        $tipoRetFederal.prop('selectedIndex', 0);
    }

    function configurarCSTCredito() {
       $divValorPIS.show();
        $divValorCOFINS.show();
        configurarValoresCalculados();
       
        mostrarCampo($baseCalcFederal);
        habilitarCampo($baseCalcFederal);

        mostrarCampo($aliqPIS);
        desabilitarCampo($aliqPIS);
        $aliqPIS.val('0,00');

        mostrarCampo($aliqCOFINS);
        desabilitarCampo($aliqCOFINS);
        $aliqCOFINS.val('0,00');

        mostrarCampo($valorPIS);
        desabilitarCampo($valorPIS);

        mostrarCampo($valorCOFINS);
        desabilitarCampo($valorCOFINS);

        $divValorCSLL.show();
        mostrarCampo($valorCSLL);
        habilitarCampo($valorCSLL);
                
        $divValorIRRF.show()
        mostrarCampo($valorIRRF);
        habilitarCampo($valorIRRF);

        $divValorCP.show();
        mostrarCampo($valorCP);
        habilitarCampo($valorCP);
    }
});
