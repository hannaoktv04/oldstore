document.addEventListener('DOMContentLoaded', function () {
    const printButton = document.getElementById("print");
    const printOut = document.getElementById("print_out");

    if (printButton && printOut) {
        printButton.addEventListener("click", function () {
            const printArea = printOut.innerHTML;
            const newWin = window.open("", "", "width=900,height=800");

            newWin.document.write(`
                <html>
                    <head>
                        <title>Purchase Order</title>
                        <link href="/css/app.css" rel="stylesheet" />
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
    }
});
