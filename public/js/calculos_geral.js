/**
 * Created by josuecamelo on 17/08/17.
 */
function calculaTotais(c1, c2, r) {
    var qtd = document.getElementById(c1);
    var unit = document.getElementById(c2);
    var total = document.getElementById(r);
    if (qtd.value.trim().length > 0) {
        if (parseFloat(qtd.value) > 0) {
            if (unit.value.trim().length > 0)
                Multiply(c1, c2, r);
        }
        else {
            alert('O campo quantidade deve ser maior que um (1).');
            qtd.focus();
        }
    }
}
function SumFields(arIds, idTotal) {
    var total = 0;
    for (var i in arIds)
        total += parseFloat($('#' +arIds[i]).val().replace(".", "").replace(",", ".") || 0);
    total = colocaMascaraBrasil(total);
    //document.forms[0][idTotal].value = total.toLocaleString();
    $('#' + idTotal).val(total.toLocaleString());
}
function CalculaValorLiquido(somaIds, subtraiIds, idTotal) {
    var totalRetencoes = 0, total = 0;
    for (var i in somaIds) {
        //totalRetencoes+=parseFloat($(somaIds[i]).value.replace(".","").replace(",",".")||0);
        //totalRetencoes+=parseFloat(document.forms[0][somaIds[i]].value.replace(".","").replace(".","").replace(".","").replace(".","").replace(".","").replace(",",".")||0);
        if ($('#' + somaIds[i]).val().length > 0) {
            totalRetencoes += parseFloat($('#' + somaIds[i]).val().replaceAll(".", "").replaceAll(",", ".") || 0);
        }
    }
    for (var j in subtraiIds) {
        //total+=parseFloat($(subtraiIds[j]).value.replace(".","").replace(",",".")||0);
        //total+=parseFloat(document.forms[0][subtraiIds[j]].value.replace(".","").replace(".","").replace(".","").replace(".","").replace(".","").replace(",",".")||0);
        if ($('#' + subtraiIds[j]).val().length > 0) {
            total += parseFloat($('#' + subtraiIds[j]).val().replaceAll(".", "").replaceAll(",", ".") || 0);
        }
    }
    total -= totalRetencoes;
    total = colocaMascaraBrasil(arredondaNumero(total));
    $('#' + idTotal).val(total.toLocaleString());
}

function ImpostoRetido(chk, total, aliq, issqnretido, issqntotal, deducoesBase, DescontoIncon) {
    //alert(document.forms[0][chk].checked);
    var valorissqn = 0;
    var valorDesconto = 0;
    $('#txtTotal2').val($('#' + total).val());
    var aliquota = $('#' + aliq).val() == '0' ? '0' : $('#' + aliq).val();
 
    if ($('#' + chk).is(':checked')) {
        valorDesconto = parseFloat($('#' + deducoesBase).val().replace(".", "").replace(".", "").replace(",", ".") || 0) + parseFloat($('#' +DescontoIncon).val().replace(".", "").replace(".", "").replace(",", ".") || 0);
        valor = parseFloat(($('#' + total).val().replace(".", "").replace(".", "").replace(".", "").replace(".", "").replace(",", ".") || 0) - valorDesconto) * (parseFloat(aliquota.replace(".", "").replace(",", ".") || 0) / 100);
        var imposto_string = new String(valor);
        if (imposto_string.indexOf('.') != -1) {// verifica se tem casa decimal
            var imposto_string_split = imposto_string.split('.'); // separa para pegar o decimal
            if (imposto_string_split[imposto_string_split.length - 1] != '' && imposto_string_split[imposto_string_split.length - 1].length >= 3) { //verifica se tem 3 casas ou mais
                if (imposto_string_split[imposto_string_split.length - 1].charAt(imposto_string_split[imposto_string_split.length - 1].length - 1) == 5) {//verifica se o ultimo numero da casa decimal é igual a 5
                    var valor_arredondar = '0.';
                    for (i = 0; i < imposto_string_split[imposto_string_split.length - 1].length - 1; i++) valor_arredondar += '0';
                    valor_arredondar += '1'; // se casa decimal = 225 então soma 0.001
                    valor += parseFloat(valor_arredondar);
                }
            }
        }
        valorissqn = Math.round(parseFloat(valor) * 100) / 100;
        valorissqn = colocaMascaraBrasil(valorissqn);
        $('#' +issqnretido).val(valorissqn.toLocaleString());
        $('#' +issqntotal).val(valorissqn);
        //FormataMoeda(issqnretido, event);
        //FormataMoeda(issqntotal, event);
        //alert(valorissqn);
    } else {
       valorDesconto = parseFloat($('#' + deducoesBase).val().replace(".", "").replace(".", "").replace(",", ".") || 0) + parseFloat($('#' + DescontoIncon).val().replace(".", "").replace(".", "").replace(",", ".") || 0);
        valor = parseFloat(($('#' + total).val().replace(".", "").replace(".", "").replace(".", "").replace(".", "").replace(",", ".") || 0) - valorDesconto) * (parseFloat(aliquota.replace(".", "").replace(",", ".") || 0) / 100);
        //alert(valorissqn);
        var imposto_string = new String(valor);
        if (imposto_string.indexOf('.') != -1) {// verifica se tem casa decimal
            var imposto_string_split = imposto_string.split('.'); // separa para pegar o decimal
            if (imposto_string_split[imposto_string_split.length - 1] != '' && imposto_string_split[imposto_string_split.length - 1].length >= 3) { //verifica se tem 3 casas ou mais
                if (imposto_string_split[imposto_string_split.length - 1].charAt(imposto_string_split[imposto_string_split.length - 1].length - 1) == 5) {//verifica se o ultimo numero da casa decimal é igual a 5
                    var valor_arredondar = '0.';
                    for (i = 0; i < imposto_string_split[imposto_string_split.length - 1].length - 1; i++) valor_arredondar += '0';
                    valor_arredondar += '1'; // se casa decimal = 225 então soma 0.001
                    valor += parseFloat(valor_arredondar);
                }
            }
        }
        valorissqn = Math.round(parseFloat(valor) * 100) / 100;
        valorissqn = colocaMascaraBrasil(valorissqn);
       
        $('#' +issqnretido).val(0);
        $('#' +issqntotal).val(valorissqn.toLocaleString());
        //alert(valorissqn);
    }
    //if(valorissqn <= 0 && valor > 0)
    //	document.forms[0][chk].checked = false;
}
function formatarAliquota() {
    $('.money').mask('00,00', { reverse: true });
}
var maxLines = 22;
var caractersLine = 60;
//var maxCaracters = maxLines * caractersLine;
var lines = 0;
var caractersInLine = 0;
function count(event, idField, idFieldOpt, idFieldOpt2) {
    var text = document.getElementById(idField).value;
    var len = 0;
    var enter = 0;
    if (idFieldOpt != null)
        text += '\n' + document.getElementById(idFieldOpt).value;
    if (idFieldOpt2 != null)
        text += '\n' + document.getElementById(idFieldOpt2).value;
    text = text.replace(/\<br\s*\/?\>/gi, '\n');
    if (/\n/.test(text))
        enter = text.match(/\n/g).length;
    var caracters = (text.length - enter * 2) + 1;
    totalLines -= caractersInLine;
    caractersInLine = 0;
    lines = Math.floor((caracters / caractersLine) + enter);
    atualiza();
    if (event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40 || event.keyCode == 16)
        return;
    if ((parseInt(totalLines) + parseInt(lines)) >= maxLines + 1) {
        //alert('totalLines + lines: ' + parseInt(totalLines) + parseInt(lines) + ' - totalLines: ' + totalLines + ' - lines: ' + lines + '. Max.: ' + maxLines);
        event.returnValue = false; event.cancelBubble = true;
    }
}
function verifica_qtd_caracteres_texto(el, qtd_max, lblMaxID) {
    var str = window.clipboardData.getData('Text');
    if (str.length > qtd_max) {
        alert('Atenção\n-O texto que está sendo colado contém mais caracteres do que o permitido.\n-O texto será reduzido à quantide máxima.');
        setTimeout('corrigeTexto(\'' + el.id + '\', \'' + lblMaxID + '\', ' + qtd_max + ')', 800);
        return false;
    }
}
function qtd_caracCont(campo, label, limite, evento) {
    if (event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40) {
        return;
    }
    if (limite == 0) {
        if (event.keyCode != 9 && event.keyCode != 13 && event.keyCode != 0) {//evt.shiftKey == false
            evento.returnValue = false; evento.cancelBubble = true; return false;
        }
    }
    var qtd_agora = document.Form1[campo].value.length;
    document.Form1[campo].maxLength = limite;
    if (qtd_agora <= limite) {
        document.getElementById(label).innerText = limite - qtd_agora;
    } else {
        if (event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40 || event.keyCode == 16) {
            return;
        }
        evento.returnValue = false; evento.cancelBubble = true;
    }
}
function ReplaceMaxLenght(field, length) {
    if (field.value.length > length) {
        field.value = field.value.substr(0, length);
    }
    qtd_caracCont('txtDescServicos', 'LblLines', 2000, event);
}