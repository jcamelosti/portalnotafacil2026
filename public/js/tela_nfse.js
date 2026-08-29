$(document).ready(function () {

    // ==========================================
    // CAMPOS TRIBUTÁRIOS FEDERAIS
    // ==========================================

    const $empresaAtividade = $('#empresa_atividade_id');
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

    const $tributacaoIssqn  = $('#ddlTribISSQN');
    const $regimeEspTrib    = $('#ddlRegimeEspecial');
    const $tipoRetencao     = $('#ddlTipoRetencao');
    const $aliquotaIssqn    = $('#txtAliquota');

    const $txtArt                   = $('#txtArt');
    const $txtCodigoObra            = $('#txtCodigoObra');
    const $txtDeducaoBaseCalculo    = $('#txtDeducaoBaseCalculo');
    const $txtBaseCalculoISS        = $('#txtBaseCalculoISS');
    const $txtAliquota              = $('#txtAliquota');
    const $txtValorISSQN            = $('#txtValorISSQN');
    const $txtValorRetido           = $('#txtValorRetido');
    const $txtPercentualTribSN      = $('#txtPercentualTribSN');
    const $ddlSituacaoTributaria    = $('#ddlSituacaoTributaria');
    const $ddlClassificacaoTributaria = $('#ddlClassificacaoTributaria');
    const $ddlIndicadorOperacao     =   $('#ddlIndicadorOperacao');
    

    //SUSPENSÕES
    $ddlSuspExig            = $('#ddlSuspExig');
    $ddlImunidade           = $('#ddlImunidade');
    $txtProcExig            = $('#txtProcExig');

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

    const $valorTotalServico     = $('#txtTotal');

    // ==========================================
    // INICIALIZAÇÃO
    // ==========================================
    
    $tipoRetFederal.closest('label').hide();
    $divBaseCalcFederal.closest('label').hide()

    desabilitarCampo($ddlSuspExig);          
    desabilitarCampo($ddlImunidade);           
    desabilitarCampo($txtProcExig);
    desabilitarCampo($regimeEspTrib);
    desabilitarCampo($txtArt);
    desabilitarCampo($tipoRetencao);
    desabilitarCampo($txtCodigoObra);
    desabilitarCampo($txtDeducaoBaseCalculo);
    desabilitarCampo($txtBaseCalculoISS);
    desabilitarCampo($txtAliquota);
    desabilitarCampo($txtValorISSQN);
    desabilitarCampo($txtValorRetido);
    desabilitarCampo($ddlSituacaoTributaria);
    desabilitarCampo($ddlClassificacaoTributaria);

    resetarTributacaoFederal();

    $tipoRetFederal.on('change', function (event) {
        event.preventDefault();

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

    $sitTribFederal.on('change', function (event) {
        event.preventDefault();

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

    $tributacaoIssqn.on('change', function ( event ) {
        event.preventDefault();

        habilitarCampo($regimeEspTrib);
        habilitarCampo($txtDeducaoBaseCalculo);
    });    

    $regimeEspTrib.on('change', function ( event ) {
        event.preventDefault();

        habilitarCampo($tipoRetencao);
        habilitarCampo($aliquotaIssqn);

        calcularValorIssqn();
    }); 

    //ddlTipoRetencao
    $tipoRetencao.on('change', function(event){
        event.preventDefault();
        tp = $(this).val();

        if(tp != 1){
            $txtValorRetido.val($txtValorISSQN.val());
        }else{
            $txtValorRetido.val('');
        }
    });
    
    function calcularValorIssqn() {
        const baseCalcIssqn = converterNumero($txtBaseCalculoISS.val());
        const aliquotaIssqn = converterNumero($aliquotaIssqn.val());
        const valorDeducao = converterNumero($txtDeducaoBaseCalculo.val());

        const valorIssqn = (baseCalcIssqn - valorDeducao ) * (aliquotaIssqn / 100);
        
        $txtBaseCalculoISS.val( formatarMoeda(baseCalcIssqn - valorDeducao) )

        $txtValorISSQN.val(formatarMoeda(valorIssqn));
        
        if( $tipoRetencao.val() != 1 ){
            $txtValorRetido.val($txtValorISSQN.val());
        }else{
            $txtValorRetido.val('');
        }
    }

    function converterNumero(valor) {
        if (!valor) {
            return 0;
        }

        valor = valor.toString().trim();

        // Remove separador de milhar e transforma vírgula em ponto
        valor = valor.replace(/\./g, '').replace(',', '.');

        const numero = parseFloat(valor);

        return isNaN(numero) ? 0 : numero;
    }

    function formatarMoeda(valor) {
        return valor.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function calcularPisCofins() {
        const base      = converterNumero($txtBaseCalcFederal.val());
        const aliqPIS   = converterNumero($txtAliqPIS.val());
        const aliqCOFINS = converterNumero($txtAliqCOFINS.val());

        // Limpa os valores se não houver base
        if (base <= 0) {
            $txtValorPis.val('');
            $txtValorCOFINS.val('');

            $txtValorPis.prop('readonly', false);
            $txtValorCOFINS.prop('readonly', false);

            return;
        }

        // Validação das alíquotas
        if (aliqPIS <= 0) {
            $txtValorPis.val('');
            $txtValorPis.prop('readonly', false);
        } else {
            const valorPIS = base * (aliqPIS / 100);

            $txtValorPis
                .val(formatarMoeda(valorPIS))
                .prop('readonly', true);
        }

        if (aliqCOFINS <= 0) {
            $txtValorCOFINS.val('');
            $txtValorCOFINS.prop('readonly', false);
        } else {
            const valorCOFINS = base * (aliqCOFINS / 100);

            $txtValorCOFINS
                .val(formatarMoeda(valorCOFINS))
                .prop('readonly', true);
        }
    }

    $baseCalcFederal.on('change', function () {
        calcularPisCofins();
    });

    $aliqPIS.on('change', function () {
        calcularPisCofins();
    });

    $aliqCOFINS.on('change', function () {
        calcularPisCofins();
    });

    $empresaAtividade.on('change', function(event){
        valorCampo = $(this).val();
        obterAtividade(valorCampo);
    });

    $ddlSituacaoTributaria.on('change', function(event){
        valorCampo = $(this).val();
        obterClassificacoesTributarias(valorCampo);
    });

    function obterClassificacoesTributarias(valorCampo){
        $.getJSON('/c/emissor/obter/classificacoes-tributarias?q=' + valorCampo, function (data) {
            //console.log(data);
            //
            $ddlClassificacaoTributaria.empty();
            $ddlClassificacaoTributaria.append('<option value="">Selecione</option>');

             $.each(data, function (_, cClassTrib) {
                $ddlClassificacaoTributaria.append(
                    $('<option>', {
                        value: cClassTrib.codigo,
                        text: cClassTrib.codigo + ' - ' + cClassTrib.descricao,
                        //selected: tribNac.id == tribNacSelecionada
                    })
                );

            });
        });
    }

    function validarCamposPisCofins() {
        let valido = true;

        const base = $baseCalcFederal.val().trim();
        const pis = $aliqPIS.val().trim();
        const cofins = $aliqCOFINS.val().trim();

        [$baseCalcFederal, $aliqPIS, $aliqCOFINS].forEach(function ($campo) {
            $campo.removeClass('border-red-500');
        });

        if (!base) {
            $baseCalcFederal.addClass('border-red-500');
            valido = false;
        }

        if (!pis) {
            $aliqPIS.addClass('border-red-500');
            valido = false;
        }

        if (!cofins) {
            $aliqCOFINS.addClass('border-red-500');
            valido = false;
        }

        return valido;
    }

    function calcularPisCofins() {
        if (!validarCamposPisCofins()) {
            $valorPIS.val('').prop('readonly', false);
            $valorCOFINS.val('').prop('readonly', false);
            return;
        }

        const base       = converterNumero($txtBaseCalcFederal.val());
        const aliqPIS    = converterNumero($txtAliqPIS.val());
        const aliqCOFINS = converterNumero($txtAliqCOFINS.val());

        const valorPIS = base * (aliqPIS / 100);
        const valorCOFINS = base * (aliqCOFINS / 100);

        $valorPIS
            .val(formatarMoeda(valorPIS))
            .prop('readonly', true);

        $valorCOFINS
            .val(formatarMoeda(valorCOFINS))
            .prop('readonly', true);
    }

    function formatoBrasileiro(valor) {
        if (valor === null || valor === undefined || valor === '') {
            return '';
        }

        return valor.toString().replace('.', ',');
    }

    function calcularPisCofins() {
        if (!validarCamposPisCofins()) {
            $valorPIS.val('').prop('readonly', false);
            $valorCOFINS.val('').prop('readonly', false);
            return;
        }

        const base       = converterNumero($baseCalcFederal.val());
        const aliqPIS    = converterNumero($aliqPIS.val());
        const aliqCOFINS = converterNumero($aliqCOFINS.val());

        const valorPIS = base * (aliqPIS / 100);
        const valorCOFINS = base * (aliqCOFINS / 100);

        $valorPIS
            .val(formatarMoeda(valorPIS))
            .prop('readonly', true);

        $valorCOFINS
            .val(formatarMoeda(valorCOFINS))
            .prop('readonly', true);
    }

    function obterAtividade(codigoAtividade){
        $.getJSON('/c/emissor/obter/percentual-atividade-mun?q=' + codigoAtividade, function (data) {
            $('#txtAliquota').val(formatoBrasileiro(data.aliquota));
        });
    }

    $ddlIndicadorOperacao.on('change', function (event) {
        event.preventDefault();
        habilitarCampo($ddlSituacaoTributaria);
    });

    $ddlSituacaoTributaria.on('change', function (event) {
        event.preventDefault();
        habilitarCampo($ddlClassificacaoTributaria);
    });

    $('#cTribNac').on('change', function (event) {
        event.preventDefault();
        consultarPercentualTribNac($(this).val());
    });

    function consultarPercentualTribNac(cTribNac){
        $.getJSON('/c/emissor/obter/percentual-trib-nac?q=' + cTribNac, function (data) {
            $('#txtPercentualTribSN').val(formatoBrasileiro(data.aliquota));
        });
    }

    $valorTotalServico.on('blur', function () {
        valorServico = $(this).val();
        $txtBaseCalculoISS.val(valorServico);
    });

    $txtDeducaoBaseCalculo.on('blur', function(event){
        event.preventDefault();
        calcularValorIssqn();

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

    function campoSomenteLeitura($campo){
        $campo.prop('readonly', true);
        $campo.css('background-color', '#fffff0');
    }

    function campoEditavel($campo){
        $campo.prop('readonly', false);
        $campo.css('background-color', '');
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
