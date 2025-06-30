document.getElementById("print").addEventListener("click", function () {
    const printArea = document.getElementById("print_out").innerHTML;
    const newWin = window.open("", "", "width=900,height=800");
    newWin.document.write(`
            <html>
                <head>
                    <title>Purchase Order</title>
                    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
                </head>
                <body>
                    <h3 class="text-center">Purchase Order</h3>
                    ${printArea}
                </body>
            </html>
        `);
    newWin.document.close();
    setTimeout(() => {
        newWin.print();
        newWin.close();
    }, 300);
});
