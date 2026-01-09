<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laporan') - {{ config('app.name') }}</title>
    
    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                margin: 0;
                padding: 20px;
                background: white !important;
                color: black !important;
            }
            .container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice-container {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            .table th,
            .table td {
                border-color: #000 !important;
                background: white !important;
                color: black !important;
            }
            .bg-gray-50 {
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact;
            }
            .text-gray-900 {
                color: #111827 !important;
            }
            .border-gray-200 {
                border-color: #e5e7eb !important;
            }
        }
        
        @page {
            size: A4;
            margin: 20mm;
        }
        
        @page :first {
            margin-top: 0;
        }
        
        .print-only {
            display: none;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0,0,0,0.1);
            z-index: 9999;
            pointer-events: none;
            font-weight: bold;
        }
    </style>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <!-- Chart.js (untuk grafik laporan) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Watermark untuk preview -->
    <div class="watermark no-print">{{ config('app.name', 'Rental System') }}</div>
    
    <!-- Header Print Only -->
    <div class="print-only text-center mb-8">
        <h1 class="text-2xl font-bold">{{ config('app.name', 'Rental System') }}</h1>
        <p class="text-gray-600">Jl. Contoh No. 123, Kota Bandung</p>
        <p class="text-gray-600">Telp: (022) 123456 | Email: info@rentalsystem.com</p>
    </div>
    
    <!-- Action Buttons -->
    <div class="no-print fixed top-4 right-4 z-50 flex space-x-2">
        <button onclick="window.print()" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center space-x-2">
            <i class="fas fa-print"></i>
            <span>Print</span>
        </button>
        <button onclick="exportToPDF()" 
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center space-x-2">
            <i class="fas fa-file-pdf"></i>
            <span>Export PDF</span>
        </button>
        <button onclick="window.history.back()" 
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
    </div>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        @yield('content')
    </div>
    
    <!-- Footer Print Only -->
    <div class="print-only mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Halaman <span id="page-number"></span> dari <span id="total-pages"></span></p>
    </div>
    
    <!-- Print Script -->
    <script>
        // Page numbering
        window.onbeforeprint = function() {
            const totalPages = Math.ceil(document.body.scrollHeight / 1123); // A4 height in px at 96dpi
            document.getElementById('total-pages').textContent = totalPages;
        };
        
        let currentPage = 1;
        window.onafterprint = function() {
            currentPage++;
            document.getElementById('page-number').textContent = currentPage;
        };
        
        // Export to PDF
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            const element = document.body;
            
            // Add watermark
            doc.setFontSize(60);
            doc.setTextColor(240, 240, 240);
            doc.text('{{ config("app.name") }}', 105, 150, { angle: 45, align: 'center' });
            doc.setTextColor(0, 0, 0);
            
            // Get title
            const title = document.title || 'Laporan';
            
            // Convert HTML to canvas then to PDF
            html2canvas(element, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 190; // A4 width in mm
                const pageHeight = 280; // A4 height in mm
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 10;
                
                doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
                
                // Add new page if content is too long
                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                
                // Save PDF
                doc.save(`${title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_${new Date().getTime()}.pdf`);
            });
        }
        
        // Print specific element
        function printElement(elementId) {
            const printContent = document.getElementById(elementId);
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = printContent.innerHTML;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    </script>
    
    @stack('scripts')
</body>
</html>