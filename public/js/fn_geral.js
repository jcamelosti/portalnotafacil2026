/**
 * Created by josuecamelo on 17/08/17.
 */
var MSIE = (document.all);
var NSN4 = (document.layers);
var __nonMSDOMBrowser = (window.navigator.appName.toLowerCase().indexOf('explorer') == -1);
var __field = null;
String.prototype.trim=function(type){switch(type){case undefined:return this.replace(/^(\s|\xA0)*/,'').replace(/(\s|\xA0)*$/,'');break;case 'alpha':return this.replace(/[A-Za-z��������������������������]/gi,'');break;case 'number':return this.replace(/\d/gi,'');break;}}
String.prototype.replaceAll = function(de, para){
    var str = this;
    var pos = str.indexOf(de);
    while (pos > -1){
        str = str.replace(de, para);
        pos = str.indexOf(de);
    }
    return (str);
}
function WebForm_TextBoxKeyHandler(event) {
    if (event.keyCode == 13) {
        var target;
        if (__nonMSDOMBrowser) {
            target = event.target;
        }
        else {
            target = event.srcElement;
        }
        if ((typeof(target) != "undefined") && (target != null)) {
            if (typeof(target.onchange) != "undefined") {
                target.onchange();
                event.cancelBubble = true;
                if (event.stopPropagation) event.stopPropagation();
                return false;
            }
        }
    }
    return true;
}
function FormataMilhar(str){
    if(str.length > 3){
        var aux = '';
        var i = 3;
        var j = 3;
        var count = str.length - i;
        while(count >= 0){
            if(count > 0){
                aux =  '.' + str.substr(str.length - i, 3) + aux;
            }else{
                aux = str.substr(0, j) + aux;
            }
            i += 3;
            if(i > str.length){
                count =  count == 0 ? -1 : 0;
                j -= i - (str.length)   ;
            }else{
                count -= 3;
            }
        }
        str = aux;
    }
    return str;
}
function SoNumeros(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (!(charCode>=48 && charCode<=57 || charCode<20))
    {
        evt.cancelBubble = true;
        evt.returnValue = false;
        evt.preventDefault();
    }
}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//  Funciona com qualquer controle: Button, Dropdownlist, txtbox, etc.
//  Metodo: Se for no href usar javascript: se não use <script>
//  Chamada: PegaFoco_AspNet('Nome_do_Campo');
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
function capturaTecla(e){
    if(MSIE){
        if(e!=null){
            tecla = event.keyCode;	//captura o ASC da tecla pressionada
            //alert(tecla);
        }else{
            tecla=7;
        }
    }else{
        //window.addEvent(Event.KEYPRESS | Event.KEYUP);
        //window.addEventListener("keyPress", myEventHandler, false);
        tecla= (e !=null ? e.which : null);
    }
    return tecla;
}
//---------------------------------------------------------------------
// Permite apenas as teclas numéricas
//---------------------------------------------------------------------
function VerificaTecla(campo,evento){
    //Modo de usar: TxtBox1.Attributes.Add("onKeyPress", "VerificaTecla(this.name,event)")
    //				TxtBox1.Attributes.Add("onBlur", "VerificaTecla(this.name,event)")
    tecla=capturaTecla(evento);
    if(tecla==0){ // retorna 0 se a tecla for especia com setinhas por exemplo
        return 0;
    }
    //esta condicao permite ele digitar apenas um sinal -
    // para se permitir valor negativo em um campo, é necessário que seu
    //id | nome possua a flasg _neg. o JS identifica esse campo e permite o sinal de - como sendo
    //o primeiro caractere
    if((campo.indexOf("_neg")!=-1)&& document.Form1[campo].value.length==0){ // encontrar string neg no nome do campo , habilita num negativos
        condicao=((tecla > 47 && tecla < 58) || tecla==45 || tecla==0); ////n�mero de 0 � 9 ou sinal de -
    }else{
        condicao=(tecla > 47 && tecla < 58 || tecla==0 ); //n�mero de 0 � 9
    }
    if(condicao)
        return 1;
    else
    if ((tecla != 8) && (tecla !=16))//teclas de combinação
    {
        if(MSIE){
            if(evento!=null){
                evento.keyCode = 0;
            }
            // }else{
            // 	evento.preventDefault(); // cancela digitação netscape
        }
        return 0;
    }
    else
        return 1;
}
//---------------------------------------------------------------------
//valida se a data est� correta
//---------------------------------------------------------------------
function valida_data(campo){
    data= document.Form1[campo].value;
    if (data==""){
        return false;
    }
    else{
        data=data.replace(" ",""); // remove espa�os
    }
    if(data.length != 10) {
        alert("Data Incorreta!");
        document.Form1[campo].focus();
        document.Form1[campo].select();
        //arrFieldsClearValue[campo] = 1;
        return false;
    }
    posbarra1=data.indexOf("/",0);
    posbarra2=data.indexOf("/",posbarra1+1);
    dia = data.substring(0,posbarra1);
    mes = data.substring(posbarra1+1 , posbarra2);
    ano = data.substring(posbarra2+1,10);
    var observacao = 0;
    var retorno = 1;
    // o sistema  aceita de 1753 a 9999, mas este escript foi limitado desde 1900
    if((ano>=1900)&&(ano<=3000)){
        if(ano > 80 && ano <= 99) // corrige o ano caso foi digitado com apenas dois numeros
            ano = 1900 + parseInt(ano,10);
        else
            ano = 2000 + parseInt(ano,10);
    }else{
        if(data.length>=8){
            observacao = 1; // retorna 2 para mudar exibir mensagem de observacao e nao de erro.
        }
    }
    if(dia.length==0 || mes.length==0 ){
        retorno = 0;
    }
    // verifica o dia valido para cada mes
    if ((dia < 1)||(dia < 1 || dia > 30) && (mes == 4 || mes == 6 || mes == 9 || mes == 11 ) || dia > 31){
        retorno =0;
    }
    // verifica se o mes e valido
    if (mes < 1 || mes > 12 ){
        retorno =0;
    }
    // verifica se � ano bissexto
    //se o resto divisao do ano por 4 for 0 o ano � bisexto sen�o o ano � normal
    if (mes == 2 && ( dia < 1 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))){
        retorno =0;
    }
    if (document.Form1[campo].value == ""){
        retorno =0;
    }
    if (retorno == 0 ) {
        alert("Data Incorreta!");
        document.Form1[campo].focus();
        document.Form1[campo].select();
        //arrFieldsClearValue[campo] = 1;
        return false;
    } else if(observacao==1){ // mensagem de observacao
        alert("OBSERVAÇÃO: Este ano não é aceito pelo sistema!");
        document.Form1[campo].focus();
        document.Form1[campo].select();
        //arrFieldsClearValue[campo] = 1;
        return false;
    }
    else{
        //arrFieldsClearValue[campo] = 0;
        return true;
    }
}
function formatarValorFone(txt){
    var t = txt.value.replace(/[^\d]/g,'');
    var mascaraEspecial = false;
    var prefixoCelular = txt.value.substring(0,5);
    t = t.replace(/^(\d{1,2})$/, '($1)');
    for(var i = 11;i<=99;i++)
    {
        if(prefixoCelular == ("("+i+")8") || prefixoCelular == ("("+i+")9")){
            mascaraEspecial = true;
        }
    }
    txt.maxLength = (mascaraEspecial)? 14 : 13;
    t = (mascaraEspecial) ? t.replace(/^(\d{2})(\d{2,5})$/, '($1)$2') : t.replace(/^(\d{2})(\d{1,4})$/, '($1)$2');
    t = (mascaraEspecial) ? t.replace(/^(\d{2})(\d{5})(\d{2,4})$/, '($1)$2-$3') : t.replace(/^(\d{2})(\d{4})(\d{1,4})$/, '($1)$2-$3');
    txt.value = t;
}
function Money2CasasDecimais(field, evento){
    /*Formato ...XXXX,XX (15:2)
     Testado em: IE, Firefox, Safari, Opera, Chrome
     */
    var e;
    var a = new Array;
    a[0] = '0,00';
    a[1] = '0,0';
    a[2] = '0,';
    if(__field!=null && __keycode==null) {
        e = (evento.which) ? evento.which : evento.keyCode;
        __keycode = e;
        if(document.all) {
            evento.keyCode=0; evento.cancelBubble=true; evento.returnValue=false;
        }else
            evento.preventDefault();
        setTimeout('Money2CasasDecimais(null,null)', 10);
        return;
    }else if((__field!=null && __keycode!=null) && (field==null && evento==null)) {
        //ao entrar aqui o campo estará vazio...
        var teclaPressionada = String.fromCharCode(__keycode);
        field = __field;
        if(__keycode != 8) {
            field.value = a[1] + teclaPressionada;
            __field = null;
            __keycode = null;
            txtSetCursorPosition(field, field.value.length);//mantém o cursor à direita do campo para que a máscara continue funcionando
        }
        return;
    }
    if(evento && evento.type == "paste"){
        evento.returnValue =  false;
        return;
    }
    if(evento && evento.type == "keypress") {
        e = (evento.which) ? evento.which : evento.keyCode;
        if(!((e > 47 && e < 58) || e == 0 || e == 8)){
            if(evento.which && evento.which != 0){
                evento.preventDefault();
                return;
            }else{
                evento.keyCode = 0;
                return;
            }
        }
        if(evento.ctrlKey){
            if(evento.which && evento.which != 0){
                evento.preventDefault();
                return;
            }else{
                evento.keyCode = 0;
                return;
            }
        }
    }
    if(e != 8){
        for(i = 0; i < 4; i++){
            if(field.value.indexOf(a[i]) > -1 && field.value.indexOf(a[i]) == 0){
                field.value = field.value.replace(a[i], '');
            }
        }
        var count = field.value.length;
        if(count + 1 < 3){
            field.value = a[count + 1] + field.value;
        }else{
            var oldValue = field.value;
            field.value = field.value.replace(',', '');
            var str = field.value.substr(0, field.value.length - 1);
            while(str.indexOf('.') > -1){
                str = str.replace('.', '');
            }
            if(str.length >= 15){
                field.value = oldValue.substr(0, 22);
                if(evento.which && evento.which != 0){
                    evento.preventDefault();
                    return;
                }else{
                    evento.keyCode = 0;
                    return;
                }
            }else{
                field.value = FormataMilhar(str) + ',' + field.value.substr((field.value.length - 1), 1);
            }
        }
    }
}
function ValidaTelefone(field, evento)
{
    evento = evento ? evento : window.event;
    //if (field.value.indexOf("(11)9")!= -1 || field.value.indexOf("(12)9")!= -1 || field.value.indexOf("(13)9")!= -1  || field.value.indexOf("(14)9")!= -1 || field.value.indexOf("(15)9")!= -1 || field.value.indexOf("(16)9")!= -1 || field.value.indexOf("(17)9")!= -1 || field.value.indexOf("(18)9")!= -1 || field.value.indexOf("(19)9")!= -1 || field.value.indexOf("(21)9")!= -1 || field.value.indexOf("(22)9")!= -1 || field.value.indexOf("(24)9")!= -1 || field.value.indexOf("(27)9")!= -1 || field.value.indexOf("(28)9")!= -1)
    //{
    if(field.value.length == 14) {
        var regexExpression = /\(?\d{2}\)?\d{5}-\d{4}/;
        field.value = field.value.replace(/[^0-9()-]/g,'');
        if(field.value != '')
        {
            if(field.value.length != 14 || !regexExpression.test(field.value))
            {
                alert('O telefone deve estar no formato (00)00000-0000.');
                if(field.value.length != 14 && !field.enabled){
                    field.value = '';
                }
                if(field.enabled){
                    field.focus();
                    field.select();
                }
                if(evento != null) {
                    if(evento.which && evento.which != 0){
                        evento.preventDefault();
                        return;
                    }else{
                        evento.keyCode = 0;
                        return;
                    }
                }
            }
        }
    } else if (field.value.length == 13) {
        var regexExpression = /\(?\d{2}\)?\d{4}-\d{4}/;
        field.value = field.value.replace(/[^0-9()-]/g,'');
        if(field.value != '')
        {
            if(field.value.length != 13 || !regexExpression.test(field.value)) {
                alert('O telefone deve estar no formato (00)0000-0000.');
                if(field.value.length != 13 && !field.enabled){
                    field.value = '';
                }
                if(field.enabled){
                    field.focus();
                    field.select();
                }
                if(evento != null) {
                    if(evento.which && evento.which != 0){
                        evento.preventDefault();
                        return;
                    }else{
                        evento.keyCode = 0;
                        return;
                    }
                }
            }
        }
    }
}
//---------------------------------------------------------------------
// Formata os campos de data durante a digitacao
//---------------------------------------------------------------------
function FormataData(campo,evento) {
    /*	Modo de usar:
     TextBox_Data.Attributes.Add("onpaste", "return false;")
     TextBox_Data.Attributes.Add("onKeyPress", "FormataData(this.name,event);")
     TextBox_Data.Attributes.Add("onBlur", "FormataData(this.name,event);") */
    var evt;
    if(evento!=null){
        evt = evento;
    }else{
        evt = (document.all)? event : e ;
    }
    var charCode = (document.all)? evt.keyCode : evt.which ;
    var hasSelection = (document.selection && document.selection.createRange().text.length > 0) || (!isNaN(parseInt($(campo).selectionStart)) && $(campo).selectionStart < $(campo).selectionEnd);
    if(hasSelection) {
        document.Form1[campo].value = '';
    }
    var tammax = 8;
    var Numero = VerificaTecla(campo,evento);
    vr=Verifica_se_Numero(campo); // ja tem o limpaformato	]
    var tam = vr.length;
    var valorCampo = '';
    if(document.getElementById(campo)!=null){
        valorCampo = document.getElementById(campo).value;
    }else{
        campo = campo.replace(/\:/g, '_'); //como o que chega vem pelo atributo name, trocar os ':' pelo '_'
        if($('#'+campo)!=null){
            valorCampo = $('#'+campo).val();
        }
    }
    var vetor = valorCampo.split("/");
    if (tam >= tammax){
        evt.cancelBubble = true; evt.returnValue = false;
    }
    else {
        if (vetor.length == 3){
            if(vetor[0].length == 1) { vetor[0] = '0' + vetor[0]; }
            if(vetor[1].length == 1) { vetor[1] = '0' + vetor[1]; }
            vr = vetor[0] + '' + vetor[1] + '' + vetor[2];
            document.Form1[campo].value = vetor[0] + '/' + vetor[1] + '/' + vetor[2];
            if (vetor[2].length == 4) {
                evt.cancelBubble = true; evt.returnValue = false;
            }
        }
    }
    tam = vr.length + Numero;
    var tecla = String.fromCharCode(evt.keyCode); tecla = parseInt(tecla);
    var dia = 0, mes = 0, diasNoMes = 31, ano = 0;
    //Se a tecla digitada n�o for num�rica (um tab ou enter por exemplo)
    if (!Numero) {
        if ( tam > 0 ) {
            if ( tam < 8) {
                document.Form1[campo].focus();
                document.Form1[campo].value = document.Form1[campo].value;
                return false;
            } else {
                if ( !valida_data(campo) ) {
                    document.Form1[campo].focus();
                    document.Form1[campo].value = '';
                    return false;
                }
            }
        }
        //Se a tecla digitada for num�rica
    } else {
        if ( (tam == 1) && (tecla > 3) ) {
            //o dia n�o pode iniciar com n�mero maior do que 3 ou seja n�o existe dia 40 ou superior
            evt.cancelBubble = true; evt.returnValue = false;
        }
        else {
            if ( tam == 2 ) {
                dia = parseInt(vr.substr( 0, 1 ));
                if ( (dia == 0 && tecla == 0) || (dia == 3 && tecla > 1) ) {
                    //o dia n�o pode ser 00 nem maior do que 31
                    evt.cancelBubble = true; evt.returnValue = false;
                }
            }
            else {
                if ( tam == 3 ) {
                    dia = parseInt(vr.substr( 0, 2 ));
                    if ( tecla > 1 ) {
                        //o m�s n�o pode iniciar com n�mero maior do que 1 ou seja n�o existe m�s 20 ou superior
                        evt.cancelBubble = true; evt.returnValue = false;
                    }
                }
                else {
                    if ( tam == 4 ) {
                        mes = parseInt(vr.substr( 2, 1 ));
                        if ( (mes == 1 && tecla > 2) || (mes == 0 && tecla == 0) ) {
                            //o m�s n�o pode ser maior do que 12 e nem 00
                            evt.cancelBubble = true; evt.returnValue = false;
                        } else {
                            dia = parseInt(vr.substr( 0, 2 ));
                            mes = parseInt(vr.substr( 2, 1 ) + tecla);
                            if ( mes == 2 ) {
                                //a quantidade de dias do m�s de fevereiro pode ser no m�ximo 29 (em anos bissextos)
                                diasNoMes = 29;
                            } else {
                                if ( mes == 4 || mes == 6 || mes == 9 || mes == 11) {
                                    diasNoMes = 30;
                                }
                            }
                            if ( dia > diasNoMes ) {
                                //altera o dia se a quantidade de dias digitada for maior do que a quantidade de dias permitida para o m�s
                                document.Form1[campo].value = diasNoMes + '/' + vr.substr( 2, 1 );
                            }
                        }
                    }
                    else {
                        if ( tam == 5 ) {
                            if ( !(tecla == 1 || tecla == 2) ) {
                                //o ano n�o pode iniciar com digitos diferentes de 1 ou 2
                                evt.cancelBubble = true; evt.returnValue = false;  }
                        }
                        else {
                            if ( tam == 6) {
                                ano = parseInt(vr.substr( 4, 1 ));
                                if ( ano == 1 && tecla < 8  ) {
                                    //o ano n�o pde ser menor do que 1800
                                    evt.cancelBubble = true; evt.returnValue = false;
                                }
                            }
                            else {
                                if ( tam == 8 ) {
                                    dia = parseInt(vr.substr( 0, 2 ));
                                    mes = parseInt(vr.substr( 2, 2 ));
                                    ano = parseInt(vr.substr( 4, 3 ) + tecla );
                                    if ( (mes == 2 && dia == 29) && (parseInt(ano / 4) != (ano / 4)) ) {
                                        //altera o dia para 28 se o ano digitado for 29 e o ano n�o for bissexto
                                        diasNoMes = 28; dia = diasNoMes; vr = diasNoMes + '' + vr.substr( 2, 2 ) + vr.substr( 4, 3 );
                                        document.Form1[campo].value = diasNoMes + '/' + vr.substr( 2, 2 ) + '/' + vr.substr( 4, 3 );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        function formataData2(){
            $('.date').mask("99/99/9999");
        }
        //Imprimir a data já válida no textbox
        if ( tam == 3 ) {
            document.Form1[campo].value = vr +  '/';
        } else {
            if ( tam == 5 ) {
                document.Form1[campo].value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, 2 ) + '/';
            } else {
                if ( tam == 8 ) {
                    document.Form1[campo].value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, 2 ) + '/' + vr.substr( 4, 4 );
                }
            }
        }
    }
}
//---------------------------------------------------------------------
// Formata os campos de moeda em tempo de digitacao
//---------------------------------------------------------------------
function FormataMoeda(campo,evento){
    /*
     Esta rotina pode ser chamada passando como parametro Uma Campo ou um Valor já definido
     txt_Media.Attributes.Add("onKeyPress", "javascript:FormataMoeda(this.name,event);")
     */
    var campoNeg			=false;
    valor					= document.Form1[campo].value;
    var hasSelection = (document.selection && document.selection.createRange().text.length > 0) || (!isNaN(parseInt($(campo).selectionStart)) && $(campo).selectionStart < $(campo).selectionEnd);
    if(hasSelection) {
        if(evento!=null) {
            var tmpNomeEvento = evento.type;
            tmpNomeEvento = tmpNomeEvento.toLowerCase();
            if(tmpNomeEvento!="blur") document.Form1[campo].value = '';
        }
    }
    if(valor<1){
        document.Form1[campo].value =valor;
    }else{
        var valor=LimpaFormato(campo);
        if(campo.indexOf("neg")!=-1){
            campoNeg=true;
        }
        var sinalNeg	=(valor.charAt(0)=="-") ? true : false;
        if (isNaN(valor)){
            document.Form1[campo].value =""		; //limpa o campo se n�o for n�mero
            return false						;
        }
        var tammax			=17					;//qtd m�xima para formatar
        var tam				=valor.length		;
        var nomeEvento		=""					;
        var valorFormatado	=""					;
        if(evento!=null){
            nomeEvento=evento.type;
            nomeEvento=nomeEvento.toLowerCase();
            switch(nomeEvento){
                case "keypress":
                    var tecla=VerificaTecla(campo,evento);// retorna 0 quando a tecla n�o � valida ou 1 quando a tecla � valida
                    tam+=tecla;
                    break;
                case "keyup":
                case "blur":
                    if(tam<=2){
                        if(valor.charAt(0)=="0"){
                            valor=parseFloat(valor);
                            if(tam==2)
                                tam--;
                        }
                        valor+='00';
                        tam+=2;
                    }
                    break;
            }
        }
        valor=valor.toString();
        if(tam<=tammax){
            //bloqueia tecla quando o usu�rio digitar centavos
            if(valor.charAt(0)=="0" && tam>=4){
                return BloqueiaTecla(evento);
            }
            if((tam > 2) && (tam <= 5) ){
                valorFormatado= valor.substr( 0, tam - 2 ) + ',' + valor.substr( tam - 2, tam ) ; }
            if((tam >= 6) && (tam <= 8)){
                valorFormatado = valor.substr( 0, tam - 5 ) + '.' + valor.substr( tam - 5, 3 ) + ',' + valor.substr( tam - 2, tam ) ; }
            if((tam >= 9) && (tam <= 11) ){
                valorFormatado = valor.substr( 0, tam - 8 ) + '.' + valor.substr( tam - 8, 3 ) + '.' + valor.substr( tam - 5, 3 ) + ',' + valor.substr( tam - 2, tam ) ; }
            if((tam >= 12) && (tam <= 14) ){
                valorFormatado = valor.substr( 0, tam - 11 ) + '.' + valor.substr( tam - 11, 3 ) + '.' + valor.substr( tam - 8, 3 ) + '.' + valor.substr( tam - 5, 3 ) + ',' + valor.substr( tam - 2, tam ) ; }
            if((tam >= 15) && (tam <= 17) ){
                valorFormatado = valor.substr( 0, tam - 14 ) + '.' + valor.substr( tam - 14, 3 ) + '.' + valor.substr( tam - 11, 3 ) + '.' + valor.substr( tam - 8, 3 ) + '.' + valor.substr( tam - 5, 3 ) + ',' + valor.substr( tam - 2, tam ) ;}
        }else{
            //Bloqueia a tecla para nao deixar o usu�rio digitar mais que o tamanho m�ximo definido para formata��o
            return BloqueiaTecla(evento);
        }
        if(valorFormatado!="")
            document.Form1[campo].value  = valorFormatado;
        //Coloca o sinal antes do valor
        if(sinalNeg && campoNeg){
            valor=document.Form1[campo].value;
            valor=valor.replace("-","");
            document.Form1[campo].value="-"+valor;
        }
    }
}
function BloqueiaTecla(evento){
    if(MSIE){
        if(evento!=null){
            evento.keyCode = 0;
        }
    }else{
        evento.preventDefault(); // cancela digitação netscape
    }
    return false;
}
function LimpaFormato(campo)
{
    // pode passar o campo como parametro ou o valor
    if(campo==""){
        return "";
    }
    if(isNaN(campo)){ // se for campo
        vr = document.Form1[campo].value;
    }else{ // se for o valor
        vr=campo;
        vr=vr.toString();
    }
    valor="";
    for (pos=0; pos < vr.length; pos ++)
    {
        sinalmenos	=vr.charAt(pos)!='-';
        sinalponto	=vr.charAt(pos)!='.';
        sinalvirgula=vr.charAt(pos)!=',';
        sinalbarra	=vr.charAt(pos)!='/';
        if ( (sinalmenos) && (sinalponto) && (sinalvirgula) && (sinalbarra) )
        {
            valor = valor + vr.charAt(pos);
        }
    }
    return valor;
}
function colocaMascaraBrasil(numero) {
    var numeroStr = '';
    numeroStr = numero.toString();
    if (numero - (Math.round(numero)) == 0) {
        numeroStr = numeroStr + ',00';
        return numeroStr;
    }
    var parteDecial = numeroStr.slice(numeroStr.indexOf('.'), numeroStr.length);
    if (parteDecial.length == 2) {
        parteDecial = parteDecial + '0';
    }
    parteDecial = parteDecial.replace('.', ',');
    var parteInteira = numeroStr.slice(0, numeroStr.indexOf('.'));
    var vetorParteInteira = [];
    for (var i = 0; i < parteInteira.length; i++) {
        vetorParteInteira.push(parteInteira.slice(i, i + 1));
    }
    //console.log(parteDecial);
    var parteInteiraFinal = '';
    var comprimento = vetorParteInteira.length - 1;
    for (var i = 0; i < vetorParteInteira.length; i++) {
        if (((((comprimento - i) + 1) / 3) - (Math.floor((((comprimento - i) + 1) / 3)))) == 0 && (((comprimento - i) + 1) != vetorParteInteira.length)) {
            parteInteiraFinal = parteInteiraFinal + '.' + vetorParteInteira[i];
        } else {
            parteInteiraFinal = parteInteiraFinal + vetorParteInteira[i];
        }
    }
    var valorFinalCorrigido = parteInteiraFinal + parteDecial;
    return valorFinalCorrigido;
}
/**********
 // Atendendo ao post: http://helpdesk.issnetonline.com.br/Messages.aspx?ThreadID=18100
 // Arredondamento Decl. Serviços Contratados - (Mal Funcionamento)
 // R$2.407,50 * 3% arredondado deve ser R$72,23 e não R$72,22.
 **********/
function arredondaNumero(valor){
    var imposto_string = new String(valor);
    if(imposto_string.indexOf('.') != -1) {// verifica se tem casa decimal
        var imposto_string_split = imposto_string.split('.'); // separa para pegar o decimal
        if(imposto_string_split[imposto_string_split.length-1]!='' && imposto_string_split[imposto_string_split.length-1].length >= 3) { //verifica se tem 3 casas ou mais
            if(imposto_string_split[imposto_string_split.length-1].charAt(imposto_string_split[imposto_string_split.length-1].length-1) == 5) {//verifica se o ultimo numero da casa decimal � igual a 5
                var valor_arredondar = '0.';
                for(i=0; i<imposto_string_split[imposto_string_split.length-1].length-1; i++) valor_arredondar += '0';
                valor_arredondar += '1'; // se casa decimal = 225 ent�o soma 0.001
                valor += parseFloat(valor_arredondar);
            }
        }
    }
    var resp = Math.round(parseFloat(valor)*100)/100;
    return resp;
}
//---------------------------------------------------------------------
// Formata os campos de telefone durante a digitacao
//---------------------------------------------------------------------
function FormataTelefone(campo,evento){
    /*
     Modo de usar:
     Txt_Fone.Attributes.Add("onKeyPress", "javascript:FormataTelefone(this.name);")
     Txt_Fone.Attributes.Add("onBlur", "javascript:FormataTelefone(this.name);")
     */
    var tammax = 8;
    document.Form1[campo].maxLength = "9";
    if ($('txtDDDFax').value == "11" || $('txtDDDFax').value == "12" || $('txtDDDFax').value == "13" || $('txtDDDFax').value == "14" ||
        $('txtDDDFax').value == "15" || $('txtDDDFax').value == "16" || $('txtDDDFax').value == "17" || $('txtDDDFax').value == "18" ||
        $('txtDDDFax').value == "19" || $('txtDDDFax').value == "21" || $('txtDDDFax').value == "22" || $('txtDDDFax').value == "24" ||
        $('txtDDDFax').value == "27" || $('txtDDDFax').value == "28" || $('txtDDDFax').value == "67" || $('txtDDDFax').value == "65") {
        var tammax = 9;
        document.Form1[campo].maxLength = "10";
    }
    //var tammax = 9;
    var Numero = VerificaTecla(campo,evento);
    vr=Verifica_se_Numero(campo); // ja tem o limpaformato
    if (!vr) {return false;}
    var tam = vr.length;
    if (tam >= tammax)
    {
        if (Numero == 0)// esta formata��o so vai ocorrer caso o usuariio colar no campo a quantidade de
        {				//caracteres maior que o tamanho maximo (tammax)
            vr = vr.slice(0, tammax);
            tam = vr.length;
        }
        else{
            return false;
        }
    }
    tam = vr.length + Numero; // soma com o retorno da funcao verificaTecla que pode ser 0 ou 1
    if ( (tam > 4) ){
        document.Form1[campo].value = vr.substr( 0, tam - 4 ) + '-' + vr.substr( tam - 4, tam ) ;
    }
}
//---------------------------------------------------------------------
// Formata cpnj durante a digitacao
//---------------------------------------------------------------------
function FormataCnpj(campo,evento)
{	/*Modo de usar:  TxtBox1.Attributes.Add("onKeyPress", "javascript:FormataCnpj(this.name);")
 TxtBox1.Attributes.Add("onBlur", "javascript:FormataCnpj(this.name);valida_cnpj(this.name);") */
    var tammax = 14  ;
    var Numero = VerificaTecla(campo,evento);
    vr=Verifica_se_Numero(campo); // ja tem o limpaformato
    if (!vr) {return false;}
    var tam = vr.length;
    if (tam >= tammax)
    {
        if (Numero == 0)// esta formatação so vai ocorrer caso o usuariio colar no campo a quantidade de
        {				//caracteres maior que o tamanho maximo (tammax)
            vr = vr.slice(0, tammax);
            tam = vr.length;
        }
        else
        {
            return false;
        }
    }
    tam = vr.length + Numero;
    if ( tam <= 2 ){
        document.Form1[campo].value = vr ; }
    if ( (tam > 2) && (tam <= 6) ){
        document.Form1[campo].value = vr.substr( 0, tam - 2 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 7) && (tam <= 9) ){
        document.Form1[campo].value = vr.substr( 0, tam - 6 ) + '/' + vr.substr( tam - 6, 4 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 10) && (tam <= 12) ){
        document.Form1[campo].value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '/' + vr.substr( tam - 6, 4 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 13) && (tam <= 14) ){
        document.Form1[campo].value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '/' + vr.substr( tam - 6, 4 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 15) && (tam <= 17) ){
        document.Form1[campo].value = vr.substr( 0, tam - 14 ) + '.' + vr.substr( tam - 14, 3 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam ) ;}
}
//---------------------------------------------------------------------
// Verifica se a string digitada é numerica
//---------------------------------------------------------------------
function Verifica_se_Numero(campo){
    /*
     Verifica se os dados digitados é numerico
     finalidade : Verificar se o valor do campo é numerico testar sempre ao sair de um campo do tipo numerico
     evitando assim que o usuario copie e cole valores indevidos.
     Obs se vc usa no evento onBlur alguma funcao do tipo Validar_cpf, Validar_Cnpj não é necessário
     fazer uso desta funcao pois a mesma ja está inclusa.
     Modo de Usar :  TextBox.Attributes.Add("onBlur", "Verifica_se_Numero(this.name)")
     */
    var valor = LimpaFormato(campo);
    if(valor!=""){
        if (isNaN(valor)){
            window.alert("O valor digitado n\u00E3o \u00E9 v\u00E1lido, verifique sua digita\u00E7\u00E3o");
            document.Form1[campo].focus();
            document.Form1[campo].select();
            return false;
        }
    }
    return valor;
} //func
//---------------------------------------------------------------------
// Formata cpf durante a digitacao
//---------------------------------------------------------------------
function FormataCpf(campo,evento)
{
    /*Modo de usar:  TxtBox1.Attributes.Add("onKeyPress", "javascript:FormataCpf(this.name,event);")
     TxtBox1.Attributes.Add("onBlur", "javascript:FormataCpf(this.name,event);valida_cpf(this.name);") */
    var tammax = 11;
    var Numero = VerificaTecla(campo,evento);
    if (Numero == 0){
        return false;
    }
    vr=Verifica_se_Numero(campo); // ja tem o limpaformato
    if (!vr) {return false;}
    var tam = vr.length;
    if (tam >= tammax)
    {
        if (Numero == 0)// esta formata��o so vai ocorrer caso o usuariio colar no campo a quantidade de
        {				//caracteres maior que o tamanho maximo (tammax)
            vr = vr.slice(0, tammax);
            tam = vr.length;
            // }
            // else{
            // 	return false;
        }
    }
    tam = vr.length + Numero;
    if ( tam <= 2 ){
        document.Form1[campo].value = vr ; }
    if ( (tam > 2) && (tam <= 5) ){
        document.Form1[campo].value = vr.substr( 0, tam - 2 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 6) && (tam <= 8) ){
        document.Form1[campo].value = vr.substr( 0, tam - 5 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 9) && (tam <= 11) ){
        document.Form1[campo].value = vr.substr( 0, tam - 8 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 12) && (tam <= 14) ){
        document.Form1[campo].value = vr.substr( 0, tam - 11 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 15) && (tam <= 17) ){
        document.Form1[campo].value = vr.substr( 0, tam - 14 ) + '.' + vr.substr( tam - 14, 3 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam ) ;}
}
//-----------
function Valida_Cpf_Cnpj(campo,evento) {
    valorcampo=document.Form1[campo].value;
    valorcampo=LimpaFormato(campo);
    if(valorcampo.length<=11){
        return valida_cpf(campo,evento);
    }else{
        return valida_cnpj(campo,evento);
    }
}
//Utilizado na declaração de serviços Contratados
function Valida_Cpf_Cnpj(campo,evento,campoEstrangeiro) {
    valorcampo=document.Form1[campo].value;
    valorcampo=LimpaFormato(campo);
    if(valorcampo.length<=11){
        return valida_cpf(campo,evento);
    }else{
        return valida_cnpj(campo,evento);
    }
}
function Formata_Cpf_Cnpj(campo,evento) {
    var titulo = '';
    valorcampo=document.Form1[campo].value;
    if(valorcampo.length<=14){
        FormataCpf(campo,evento);
        //titulo="Consulta por CPF";
    }else{
        FormataCnpj(campo,evento);
        //titulo="Consulta por CNPJ";
    }
    if(titulo!=''){
        document.Form1[campo].title= titulo;
    }
}

//---------------------------------------------------------------------
// VALIDAR "CPF"
//---------------------------------------------------------------------	
function valida_cpf(campo,evento) 
{ 

	var Numero = VerificaTecla(campo,evento);
	vr=Verifica_se_Numero(campo); // ja tem o limpaformato
	if (!vr) {
		
		evento.returnValue =  false;
		if(evento.which && evento.which != 0) evento.preventDefault();
		else evento.keyCode = 0;
		return false;
	}
	
		cpf = vr;        
        Retorno = 1;
		var numeros, digitos, soma, i, resultado, digitos_iguais; 
		digitos_iguais = 1; 
		if (cpf.length < 11) 
            Retorno = 0 ; 
		 for (i = 0; i < cpf.length - 1; i++) 
            if (cpf.charAt(i) != cpf.charAt(i + 1)) 
            { 
              digitos_iguais = 0; 
              break; 
            } 
		if (!digitos_iguais) 
		{ 
            numeros = cpf.substring(0,9); 
            digitos = cpf.substring(9); 
            soma = 0; 
            for (i = 10; i > 1; i--) 
                  soma += numeros.charAt(10 - i) * i; 
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11; 
            if (resultado != digitos.charAt(0)) 
                  Retorno = 0;
            numeros = cpf.substring(0,10); 
            soma = 0; 
            for (i = 11; i > 1; i--) 
                  soma += numeros.charAt(11 - i) * i; 
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11; 
            if (resultado != digitos.charAt(1)) 
                 Retorno = 0;
		} 
			else 
				Retorno = 0; 
	  if(Retorno==0)
      {
		if(document.Form1[campo].value!="")
		{	
			document.Form1[campo].value = ""
			window.alert("CPF Incorreto.");
			evento.returnValue =  false;
			if(evento.which && evento.which != 0) evento.preventDefault();
			else evento.keyCode = 0;
			//document.Form1[campo].focus();
			
			return false;
		}
	  } else {
		return true;
	  }
 }

 //---------------------------------------------------------------------
// FUNÇÃO PARA VALIDAR NÚMERO DE "CNPJ"
//---------------------------------------------------------------------	

function valida_cnpj(campo,evento) 
{ 	/*esta funcao deve ser chamado junto com o formata_cnpj ver exemplo na funcao formata_cnpj */
	
	var Numero = VerificaTecla(campo,evento);
	vr=Verifica_se_Numero(campo); // ja tem o limpaformato
	if (!vr) {return false;}
	
	cnpj = vr
	
	retorno=1;
   
     var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais; 
     digitos_iguais = 1; 
      if (cnpj.length < 14 ){retorno=0;}
     
     for (i = 0; i < cnpj.length - 1; i++)
     { 
           if (cnpj.charAt(i) != cnpj.charAt(i + 1)) 
           { 
                digitos_iguais = 0; 
                break; 
           } 
     }
      
      if (!digitos_iguais) 
       { 

            tamanho = cnpj.length - 2 
            numeros = cnpj.substring(0,tamanho); 
            digitos = cnpj.substring(tamanho); 
            soma = 0; 
            pos = tamanho - 7; 
            for (i = tamanho; i >= 1; i--) 
                  { 
                  soma += numeros.charAt(tamanho - i) * pos--; 
                  if (pos < 2) 
                        pos = 9; 
                  } 
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11; 
            if (resultado != digitos.charAt(0))
            { 
                  retorno=0;//return false; 
            }
            tamanho = tamanho + 1; 
            numeros = cnpj.substring(0,tamanho); 
            soma = 0; 
            pos = tamanho - 7; 
            for (i = tamanho; i >= 1; i--) 
                  { 
                  soma += numeros.charAt(tamanho - i) * pos--; 
                  if (pos < 2) 
                        pos = 9; 
                  } 
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11; 
            if (resultado != digitos.charAt(1)) 
            {
                  retorno=0;
             }

       } 
      else {
			retorno=0;
            //return false; 
      } 
      
      if(retorno==0)
      {
		document.Form1[campo].value = ""
		window.alert("CNPJ Incorreto.")
		//document.Form1[campo].focus();
		return 0;
      } else {
		return true;
      }
      
 }//function