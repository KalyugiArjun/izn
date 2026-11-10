<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>कार्यकुशलता प्रमाण-पत्र - {{ $nomination->certificate_no ?? 'Award Certificate' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700;800;900&family=Noto+Sans+Devanagari:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            background-color: #0f172a;
            font-family: 'Inter', 'Noto Sans Devanagari', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: geometricPrecision;
        }

        /* Top Action Bar */
        .action-bar {
            width: 1024px;
            max-width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 12px 24px;
            background: #1e293b;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .action-bar .cert-meta {
            color: #f8fafc;
            font-size: 14px;
        }
        .action-bar .cert-meta strong {
            color: #38bdf8;
            font-family: monospace;
            font-size: 15px;
        }
        .action-btns {
            display: flex;
            gap: 12px;
        }
        .btn-print {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            border: none;
            padding: 9px 24px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            transition: transform 0.15s ease;
        }
        .btn-print:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }
        .btn-close-cert {
            background: #334155;
            color: #cbd5e1;
            border: none;
            padding: 9px 18px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-close-cert:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Certificate Container (1024px x 724px) */
        .cert-outer-wrapper {
            width: 1024px;
            max-width: 100%;
            display: flex;
            justify-content: center;
        }
        .certificate-canvas {
            position: relative;
            width: 1024px;
            height: 724px;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5);
            image-rendering: -webkit-optimize-contrast;
            image-rendering: high-quality;
        }
        .certificate-bg-img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: fill;
            user-select: none;
            -webkit-user-drag: none;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: high-quality;
        }

        /* Dynamic Field Overlay Layer */
        .cert-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .cert-text {
            position: absolute;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #102a54;
            font-weight: 800;
            white-space: nowrap;
            letter-spacing: 0.3px;
            line-height: 1.2;
            text-shadow: 0 0 1px rgba(16, 42, 84, 0.2);
        }

        /* 1. Nominee Name: Fits perfectly between "प्रमाणित किया जाता है..." and the golden line */
        .cert-name {
            top: 50.8%;
            font-size: 30px;
            font-weight: 900;
            color: #0d234a;
            letter-spacing: 0.5px;
        }

        /* 2. Designation: Fits cleanly right below the golden line, above "ने अपने दायित्वों..." */
        .cert-desig {
            top: 57.5%;
            font-size: 20px;
            font-weight: 800;
            color: #102a54;
        }

        /* 3. Proficiency: Fits perfectly between "ने अपने दायित्वों..." and "में उत्कृष्ट..." */
        .cert-prof {
            top: 66.2%;
            font-size: 24px;
            font-weight: 900;
            color: #0d234a;
        }

        /* 4. Date: Fits cleanly below "यह प्रमाण-पत्र उनकी कार्यकुशलता...", above Certificate Number */
        .cert-date {
            top: 79.4%;
            font-size: 20px;
            font-weight: 800;
            color: #102a54;
        }

        /* 5. Certificate Number: Fits directly above the bottom golden ornament */
        .cert-no {
            top: 84.8%;
            font-size: 22px;
            font-weight: 900;
            color: #0d234a;
            letter-spacing: 0.8px;
        }

        /* Print Specific Styling */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body {
                background: none !important;
                margin: 0 !important;
                padding: 0 !important;
                min-height: auto !important;
            }
            .action-bar {
                display: none !important;
            }
            .cert-outer-wrapper {
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .certificate-canvas {
                width: 100vw !important;
                height: 100vh !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar --}}
    <div class="action-bar no-print">
        <div class="cert-meta">
            <span>प्रमाण-पत्र संख्या: </span>
            <strong>{{ $nomination->certificate_no ?? 'NER/IZN/OPTG/----' }}</strong>
            <span class="mx-2 text-muted">|</span>
            <span>नाम: <strong>{{ $nomination->name ?? ($nomination->staff->name ?? '-') }}</strong></span>
        </div>
        <div class="action-btns">
            <button onclick="window.print()" class="btn-print">
                <i class="ti ti-printer"></i> प्रिंट प्रमाण-पत्र (Print)
            </button>
            <a href="javascript:window.close();" class="btn-close-cert">
                बंद करें (Close)
            </a>
        </div>
    </div>

    {{-- Certificate Container with Background Image and Overlay Fields --}}
    <div class="cert-outer-wrapper">
        <div class="certificate-canvas" id="printableCertificate">
            {{-- Template Background Image (User Uploaded Template) --}}
            <img src="{{ asset('assets/images/award_certificate_bg.jpg') }}" alt="Certificate Template" class="certificate-bg-img">

            {{-- Dynamic Fields Overlay --}}
            <div class="cert-overlay">
                {{-- 1. Name: Directly between 'प्रमाणित किया जाता है...' and golden line --}}
                <div class="cert-text cert-name">
                    {{ $nomination->name ?? ($nomination->staff->name ?? '') }}
                </div>

                {{-- 2. Designation: Directly below golden line, above 'ने अपने दायित्वों...' --}}
                <div class="cert-text cert-desig">
                    पद : {{ $nomination->designation->name ?? '' }}
                </div>

                {{-- 3. Area of Proficiency: Directly between 'ने अपने दायित्वों...' and 'में उत्कृष्ट...' --}}
                <div class="cert-text cert-prof">
                    {{ $nomination->proficiency_cert->name ?? ($nomination->proficiency->name ?? 'Safe Train Operation') }}
                </div>

                {{-- 4. Date: Directly below 'यह प्रमाण-पत्र उनकी...' --}}
                <div class="cert-text cert-date">
                    दिनांक : {{ $nomination->checked_date ? \Carbon\Carbon::parse($nomination->checked_date)->format('d-m-Y') : ($nomination->date ? \Carbon\Carbon::parse($nomination->date)->format('d-m-Y') : date('d-m-Y')) }}
                </div>

                {{-- 5. Certificate Number: Directly above the golden bottom ornament --}}
                <div class="cert-text cert-no">
                    {{ $nomination->certificate_no ?? 'NER/IZN/OPTG/0002' }}
                </div>
            </div>
        </div>
    </div>

</body>
</html>
