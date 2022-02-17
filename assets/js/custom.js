document.getElementById('downloadpdf').onclick = function () {
	// Your html2pdf code here.
	var element = document.getElementById('my-lesson');
    var opt = {
        margin: 1,
        filename: 'my_izido.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: {
            scale: 2,
            useCORS: true,
        },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
    

    /* var scaleBy = 5;
    var w = 1000;
    var h = 1000;
    var div = document.querySelector('#my-lesson');
    var canvas = document.createElement('canvas');
    // canvas.width = w * scaleBy;
    // canvas.height = h * scaleBy;
    // canvas.style.width = w + 'px';
    // canvas.style.height = h + 'px';
    var context = canvas.getContext('2d');
    context.scale(scaleBy, scaleBy);

    html2canvas(div, {
        canvas:canvas,
        onrendered: function (canvas) {
            theCanvas = canvas;
            document.body.appendChild(canvas);

            Canvas2Image.saveAsPNG(canvas);
            $(body).append(canvas);
        }
    }); */

};

