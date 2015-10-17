function layout(margin_top)
{
    var width = $('body').css('width');

    width = filterPX(width);

    var height = width * 2.85;

    var margin_top = height * margin_top * -1;

    $('#bg').css('width', width);
    $('#bg').css('height', height);
    $('#menu').css('margin-top', margin_top);

    var margin_btn = width/2 * 0.25;

    $('.left').css('margin-left', margin_btn);
    $('.right').css('margin-right', margin_btn);

    resizeBtn(width*0.3);
}

function resizeBtn(width)
{
    var height = width * 0.21;
    $('.btn').css('width', width);
    $('.btn').css('height', height);
}

function filterPX(input)
{
    var output = input.toString();
    output = output.slice(0, output.length -2);
    return output;
}