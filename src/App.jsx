import React, { useState, useEffect } from 'react';
import {
    Upload, ArrowRight, CheckCircle2, AlertCircle,
    Fuel, FileText, Download, History,
    Smartphone, FileArchive, RefreshCw, Car, Sliders, ChevronLeft,
    HelpCircle, ExternalLink, Globe
} from 'lucide-react';

/**
 * Pobiera maksymalny rozmiar pliku z zmiennych środowiskowych z fallbackiem
 */
const getMaxSizeMB = () => {
    try {
        const envSize = (typeof process !== 'undefined' && process.env?.VITE_MAX_FILE_SIZE_MB) ||
            (typeof import.meta !== 'undefined' && import.meta.env?.VITE_MAX_FILE_SIZE_MB);
        return envSize ? parseInt(envSize) : 20;
    } catch (e) {
        return 20;
    }
};

const MAX_FILE_SIZE_MB = getMaxSizeMB();
const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

const TRANSLATIONS = {
    pl: {
        title: "FuelioImport",
        subtitle: "Konwerter danych",
        pwa_install: "Zainstaluj aplikację",
        plus_circle: "Dodaj",
        step1_title: "Z jakiej aplikacji migrujesz?",
        step1_desc: "Wybierz format źródłowy, abyśmy wiedzieli jak odczytać Twoje dane.",
        step2_back: "Wróć do wyboru formatu",
        step2_title: "Prześlij plik {name}",
        step2_desc: "Plik zostanie przesłany do bezpiecznego przetworzenia przez silnik PHP.",
        drop_zone: "Dotknij tutaj lub przeciągnij plik {ext}",
        browse: "Wybierz plik",
        step3_title: "Konfiguracja importu",
        step3_desc: "Dostosuj parametry konwertera przed przetworzeniem.",
        vehicle_name: "Nazwa pojazdu (opcjonalnie)",
        vehicle_placeholder: "np. Mój Volkswagen",
        converter_options: "Opcje specyficzne dla formatu",
        warning: "Fuelio obsługuje tylko jeden pojazd na plik. Dane zostaną scalone w jeden profil.",
        convert_btn: "Rozpocznij konwersję",
        converting: "Przetwarzanie...",
        success_title: "Konwersja zakończona!",
        success_desc: "Twój plik został poprawnie przetworzony. Jest gotowy do wgrania w Fuelio.",
        download_btn: "Pobierz plik Fuelio",
        convert_another: "Konwertuj inny plik",
        footer: "© 2026 FuelioImport. Open Source Project.",
        error_server: "Błąd serwera podczas przetwarzania. Upewnij się, że plik jest poprawny.",
        error_connection: "Nie udało się połączyć z silnikiem konwersji.",
        error_empty: "Silnik PHP nie zwrócił żadnych danych.",
        error_too_large: "Plik jest zbyt duży. Maksymalny rozmiar to {size}MB.",
        label_vehicle_id: "ID Eksportowanego pojazdu",
        label_dist_unit: "Jednostka dystansu",
        label_fuel_unit: "Jednostka paliwa",
        unit_km: "Kilometry (km)",
        unit_mi: "Mile (mi)",
        unit_l: "Litry (l)",
        unit_gal_us: "Galony (US)",
        unit_gal_uk: "Galony (UK)",
        help: "Pomoc",
        visit_motostat: "Otwórz Motostat"
    },
    de: {
        title: "FuelioImport",
        subtitle: "Datenkonverter",
        pwa_install: "App installieren",
        plus_circle: "Hinzufügen",
        step1_title: "Von welcher App migrieren Sie?",
        step1_desc: "Wählen Sie das Quellformat aus.",
        step2_back: "Zurück",
        step2_title: "{name}-Datei hochladen",
        step2_desc: "Die Datei wird zur Verarbeitung an die PHP-Engine gesendet.",
        drop_zone: "Hier tippen oder {ext}-Datei herziehen",
        browse: "Datei auswählen",
        step3_title: "Konfiguration",
        step3_desc: "Passen Sie die Konverterparameter an.",
        vehicle_name: "Fahrzeugname",
        vehicle_placeholder: "z.B. Mein Volkswagen",
        converter_options: "Konverter-Optionen",
        warning: "Fuelio unterstützt nur ein Fahrzeug pro Datei.",
        convert_btn: "Konvertierung starten",
        converting: "Verarbeitung...",
        success_title: "Abgeschlossen!",
        success_desc: "Ihre Datei wurde erfolgreich verarbeitet.",
        download_btn: "Datei herunterladen",
        convert_another: "Weitere Datei",
        footer: "© 2026 FuelioImport. Open Source Project.",
        error_server: "Serverfehler bei der Verarbeitung.",
        error_connection: "Verbindung fehlgeschlagen.",
        error_empty: "Keine Daten empfangen.",
        error_too_large: "Datei zu groß. Max: {size}MB.",
        label_vehicle_id: "Fahrzeug-ID",
        label_dist_unit: "Entfernungseinheit",
        label_fuel_unit: "Kraftstoffeinheit",
        unit_km: "Kilometer (km)",
        unit_mi: "Meilen (mi)",
        unit_l: "Liter (l)",
        unit_gal_us: "Gallonen (US)",
        unit_gal_uk: "Gallonen (UK)",
        help: "Hilfe",
        visit_motostat: "Motostat öffnen"
    },
    en: {
        title: "FuelioImport",
        subtitle: "Data converter",
        pwa_install: "Install App",
        plus_circle: "Add",
        step1_title: "Source Application?",
        step1_desc: "Select the source format for data processing.",
        step2_back: "Back",
        step2_title: "Upload {name} file",
        step2_desc: "File will be sent for secure processing via PHP engine.",
        drop_zone: "Tap here or drag {ext} file",
        browse: "Choose File",
        step3_title: "Import configuration",
        step3_desc: "Adjust converter parameters before processing.",
        vehicle_name: "Vehicle name",
        vehicle_placeholder: "e.g. My Volkswagen",
        converter_options: "Format specific options",
        warning: "Fuelio supports one vehicle per file. Data will be merged.",
        convert_btn: "Start conversion",
        converting: "Processing...",
        success_title: "Success!",
        success_desc: "Your file has been successfully processed.",
        download_btn: "Download Fuelio file",
        convert_another: "Convert another",
        footer: "© 2026 FuelioImport. Open Source Project.",
        error_server: "Server error. Ensure the file is valid.",
        error_connection: "Could not connect to engine.",
        error_empty: "PHP engine returned no data.",
        error_too_large: "File too large. Max: {size}MB.",
        label_vehicle_id: "Vehicle ID",
        label_dist_unit: "Distance unit",
        label_fuel_unit: "Fuel unit",
        unit_km: "Kilometers (km)",
        unit_mi: "Miles (mi)",
        unit_l: "Liters (l)",
        unit_gal_us: "Gallons (US)",
        unit_gal_uk: "Gallons (UK)",
        help: "Help",
        visit_motostat: "Open Motostat"
    }
};

export default function App() {
    const getInitialLanguage = () => {
        if (typeof navigator === 'undefined') return 'en';
        const browserLang = navigator.language.split('-')[0];
        return TRANSLATIONS[browserLang] ? browserLang : 'en';
    };

    const [lang, setLang] = useState(getInitialLanguage());
    const [step, setStep] = useState(1);
    const [selectedFormat, setSelectedFormat] = useState(null);
    const [file, setFile] = useState(null);
    const [vehicleName, setVehicleName] = useState('');
    const [converterSettings, setConverterSettings] = useState({});
    const [isProcessing, setIsProcessing] = useState(false);
    const [result, setResult] = useState(null);
    const [error, setError] = useState(null);
    const [deferredPrompt, setDeferredPrompt] = useState(null);

    const t = TRANSLATIONS[lang];

    const FORMATS = [
        {
            id: 'acar',
            name: 'aCar',
            ext: '.abp',
            prefix: 'acar',
            Icon: FileArchive,
            color: 'bg-purple-600',
            settings: [{ key: 'vehicle_id', label: t.label_vehicle_id, type: 'number', min: 1, default: 1 }],
            actions: [{ label: t.help, url: 'https://github.com/Programistyk/FuelioImport/wiki/Converters---aCar', icon: HelpCircle }]
        },
        {
            id: 'fuellog',
            name: 'Fuel Log',
            ext: '.csv',
            prefix: 'fuellog',
            Icon: History,
            color: 'bg-green-500',
            settings: [{ key: 'vehicle_id', label: t.label_vehicle_id, type: 'number', min: 1, default: 1 }],
            actions: [{ label: t.help, url: 'https://github.com/Programistyk/FuelioImport/wiki/Converters---FuelLog', icon: HelpCircle }]
        },
        {
            id: 'drivvo',
            name: 'Drivvo',
            ext: '.csv',
            prefix: 'drivvo',
            Icon: Car,
            color: 'bg-orange-500',
            settings: [
                {
                    key: 'dist_unit',
                    label: t.label_dist_unit,
                    type: 'select',
                    options: [{v: 0, l: t.unit_km}, {v: 1, l: t.unit_mi}],
                    default: 0
                },
                {
                    key: 'fuel_unit',
                    label: t.label_fuel_unit,
                    type: 'select',
                    options: [{v: 0, l: t.unit_l}, {v: 1, l: t.unit_gal_us}, {v: 2, l: t.unit_gal_uk}],
                    default: 0
                }
            ],
            actions: [{ label: t.help, url: 'https://github.com/Programistyk/FuelioImport/wiki/Converters---Drivvo', icon: HelpCircle }]
        },
        {
            id: 'motostat',
            name: 'Motostat',
            ext: '.csv',
            prefix: null,
            Icon: FileText,
            color: 'bg-blue-500',
            settings: [],
            actions: [
                { label: t.help, url: 'https://github.com/Programistyk/FuelioImport/wiki/Converters---Motostat', icon: HelpCircle }
            ]
        },
    ];

    useEffect(() => {
        const handler = (e) => { e.preventDefault(); setDeferredPrompt(e); };
        window.addEventListener('beforeinstallprompt', handler);
        return () => window.removeEventListener('beforeinstallprompt', handler);
    }, []);

    const selectFormat = (f) => {
        setSelectedFormat(f);
        const defaults = {};
        f.settings?.forEach(s => defaults[s.key] = s.default);
        setConverterSettings(defaults);
        setStep(2);
        setError(null);
    };

    const handleFileChange = (e) => {
        const selectedFile = e.target.files[0];
        if (!selectedFile) return;

        if (selectedFile.size > MAX_FILE_SIZE_BYTES) {
            setError(t.error_too_large.replace('{size}', MAX_FILE_SIZE_MB));
            return;
        }

        setFile(selectedFile);
        setStep(3);
        setError(null);
    };

    const handleConversion = async () => {
        setIsProcessing(true);
        setError(null);
        const formData = new FormData();

        formData.append('f', file);
        formData.append('c', selectedFormat.id);
        formData.append('vehicle_name', vehicleName);

        if (selectedFormat.prefix && selectedFormat.settings) {
            Object.keys(converterSettings).forEach(key => {
                formData.append(`${selectedFormat.prefix}[${key}]`, converterSettings[key]);
            });
        }

        try {
            const response = await fetch('convert.php', { method: 'POST', body: formData });
            if (!response.ok) throw new Error('Server');

            let serverFileName = `fuelio_export_${selectedFormat.id}.csv`;
            const contentDisposition = response.headers.get('Content-Disposition');
            if (contentDisposition) {
                const fileNameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (fileNameMatch?.[1]) serverFileName = fileNameMatch[1].replace(/['"]/g, '');
            }

            const blob = await response.blob();
            if (blob.size === 0) throw new Error('Empty');

            setResult({ downloadUrl: URL.createObjectURL(blob), fileName: serverFileName });
            setStep(4);
        } catch (e) {
            setError(e.message === 'Empty' ? t.error_empty : t.error_server);
        } finally {
            setIsProcessing(false);
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 flex flex-col items-center font-sans antialiased">
            {deferredPrompt && (
                <div className="w-full bg-blue-600 p-3 text-white flex items-center justify-between sticky top-0 z-50 shadow-md">
                    <div className="flex items-center gap-3 ml-2 text-sm font-bold"><Smartphone size={18} /> {t.pwa_install}</div>
                    <button onClick={() => deferredPrompt.prompt()} className="bg-white text-blue-600 px-4 py-1.5 rounded-full text-xs font-black uppercase active:scale-95 transition-transform">{t.plus_circle}</button>
                </div>
            )}

            <header className="w-full max-w-6xl px-6 py-10 flex items-center justify-between">
                <div className="flex items-center gap-5">
                    <div className="p-4 bg-blue-600 rounded-2xl shadow-xl text-white transform -rotate-3 hover:rotate-0 transition-transform"><Fuel size={32} /></div>
                    <div>
                        <h1 className="text-3xl font-black tracking-tight leading-none mb-1 text-slate-800">{t.title}</h1>
                        <p className="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">{t.subtitle}</p>
                    </div>
                </div>
                <div className="flex gap-2">
                    {['pl', 'de', 'en'].map(l => (
                        <button key={l} onClick={() => setLang(l)} className={`px-3 py-2 rounded-xl border text-[10px] font-black transition-all ${lang === l ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-slate-400 border-slate-200 hover:border-blue-300'}`}>{l.toUpperCase()}</button>
                    ))}
                </div>
            </header>

            <main className="w-full max-w-5xl px-6 pb-20">
                <div className="w-full max-w-xl mx-auto mb-10 flex gap-2">
                    {[1, 2, 3, 4].map(i => (
                        <div key={i} className={`h-2 flex-1 rounded-full transition-all duration-500 ${step >= i ? 'bg-blue-600 shadow-lg shadow-blue-200' : 'bg-slate-200'}`}></div>
                    ))}
                </div>

                <div className="bg-white rounded-[3.5rem] shadow-2xl p-8 md:p-12 border border-white min-h-[550px] flex flex-col relative overflow-hidden transition-all">
                    {step === 1 && (
                        <div className="animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <h2 className="text-4xl font-black mb-3 tracking-tight text-slate-800">{t.step1_title}</h2>
                            <p className="text-slate-500 mb-10 font-medium">{t.step1_desc}</p>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                {FORMATS.map(f => (
                                    <div key={f.id} className="relative group">
                                        <button onClick={() => selectFormat(f)} className="w-full flex items-center p-6 rounded-[2.5rem] bg-slate-50 hover:bg-white hover:shadow-xl border-2 border-transparent hover:border-blue-100 transition-all text-left">
                                            <div className={`${f.color} p-4 rounded-2xl text-white mr-5 shadow-lg group-hover:scale-110 transition-transform`}><f.Icon size={20} /></div>
                                            <div className="flex-1">
                                                <div className="font-black text-slate-800 text-lg leading-tight">{f.name}</div>
                                                <div className="text-[10px] text-slate-400 font-black uppercase tracking-widest">{f.ext}</div>
                                            </div>
                                            <ArrowRight size={20} className="text-slate-200 group-hover:text-blue-500 transition-colors" />
                                        </button>
                                        {/* Quick Action Buttons */}
                                        <div className="absolute top-4 right-14 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            {f.actions?.map((action, idx) => (
                                                <a key={idx} href={action.url} target="_blank" rel="noopener noreferrer" className="p-2 bg-white rounded-full text-slate-400 hover:text-blue-600 shadow-sm border border-slate-100 transition-colors" title={action.label}>
                                                    <action.icon size={16} />
                                                </a>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {step === 2 && (
                        <div className="flex-1 flex flex-col animate-in fade-in slide-in-from-right-4 duration-500">
                            <div className="flex items-center justify-between mb-6">
                                <button onClick={() => setStep(1)} className="text-blue-600 text-[10px] font-black uppercase flex items-center gap-2 hover:translate-x-[-4px] transition-transform"><ChevronLeft size={14} /> {t.step2_back}</button>
                                <div className="flex gap-2">
                                    {selectedFormat?.actions?.map((action, idx) => (
                                        <a key={idx} href={action.url} target="_blank" rel="noopener noreferrer" className="px-3 py-1.5 bg-slate-50 rounded-xl text-slate-500 text-[10px] font-black uppercase border border-slate-100 hover:bg-white hover:shadow-md transition-all flex items-center gap-2">
                                            <action.icon size={12} /> {action.label}
                                        </a>
                                    ))}
                                </div>
                            </div>
                            <h2 className="text-4xl font-black mb-3 tracking-tight text-slate-800">{t.step2_title.replace('{name}', selectedFormat?.name)}</h2>
                            <p className="text-slate-500 mb-8 font-medium">{t.step2_desc}</p>
                            {error && <div className="p-4 bg-red-50 text-red-600 rounded-2xl mb-4 font-bold flex items-center gap-2 text-sm border border-red-100"><AlertCircle size={20} /> {error}</div>}
                            <label className="flex-1 border-4 border-dashed border-slate-100 rounded-[3rem] flex flex-col items-center justify-center p-10 hover:bg-blue-50 hover:border-blue-200 cursor-pointer transition-all group">
                                <input type="file" className="hidden" accept={selectedFormat?.ext} onChange={handleFileChange} />
                                <div className="w-24 h-24 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-xl mb-6 group-hover:scale-105 transition-transform"><Upload size={40} /></div>
                                <div className="text-lg font-black text-slate-800 mb-6 text-center">{t.drop_zone.replace('{ext}', selectedFormat?.ext)}</div>
                                <div className="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl group-hover:bg-blue-700 transition-colors">{t.browse}</div>
                            </label>
                        </div>
                    )}

                    {step === 3 && (
                        <div className="flex-1 flex flex-col animate-in fade-in slide-in-from-right-4 duration-500">
                            <button onClick={() => { setFile(null); setStep(2); }} className="text-blue-600 text-[10px] font-black uppercase mb-6 flex items-center gap-2 hover:translate-x-[-4px] transition-transform w-fit"><ChevronLeft size={14} /> {t.step2_back}</button>
                            <h2 className="text-4xl font-black mb-3 tracking-tight text-slate-800">{t.step3_title}</h2>
                            <div className="space-y-8 mt-6 overflow-y-auto pr-2 max-h-[450px] custom-scrollbar pb-4">
                                <div className="bg-slate-50 rounded-3xl p-5 border border-slate-100 flex items-center gap-4 shadow-sm">
                                    <div className={`p-3 rounded-xl shadow-sm text-white ${selectedFormat?.color}`}><selectedFormat.Icon size={20}/></div>
                                    <div className="min-w-0">
                                        <div className="font-black text-slate-800 text-sm truncate">{file?.name}</div>
                                        <div className="text-[10px] text-slate-400 font-bold uppercase">{selectedFormat?.name}</div>
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">{t.vehicle_name}</label>
                                    <div className="relative">
                                        <Car className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300" size={18} />
                                        <input type="text" value={vehicleName} onChange={(e) => setVehicleName(e.target.value)} placeholder={t.vehicle_placeholder} className="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-6 font-bold text-slate-700 focus:border-blue-500 focus:bg-white outline-none transition-all shadow-inner" />
                                    </div>
                                </div>

                                {selectedFormat?.settings?.length > 0 && (
                                    <div className="animate-in fade-in slide-in-from-bottom-2 duration-700">
                                        <label className="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4 ml-1 flex items-center gap-2"><Sliders size={12} /> {t.converter_options}</label>
                                        <div className="grid grid-cols-1 gap-5">
                                            {selectedFormat.settings.map(s => (
                                                <div key={s.key} className="bg-slate-50 p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                                                    <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                        <span className="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                                        {s.label}
                                                    </div>

                                                    {s.type === 'select' ? (
                                                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                            {s.options.map(opt => (
                                                                <button
                                                                    key={opt.v}
                                                                    onClick={() => setConverterSettings({...converterSettings, [s.key]: opt.v})}
                                                                    className={`px-4 py-3 rounded-2xl text-xs font-bold transition-all border-2 ${converterSettings[s.key] === opt.v ? 'bg-white border-blue-600 text-blue-600 shadow-md' : 'bg-white border-transparent text-slate-400 hover:border-slate-200'}`}
                                                                >
                                                                    {opt.l}
                                                                </button>
                                                            ))}
                                                        </div>
                                                    ) : (
                                                        <input
                                                            type="number"
                                                            min={s.min}
                                                            value={converterSettings[s.key] || s.default}
                                                            onChange={(e) => setConverterSettings({...converterSettings, [s.key]: parseInt(e.target.value) || s.min})}
                                                            className="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3.5 font-bold text-sm text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm"
                                                        />
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                <div className="p-5 bg-blue-50 border border-blue-100 rounded-[2rem] flex items-start gap-4 text-blue-800 text-xs font-bold leading-relaxed shadow-sm">
                                    <AlertCircle size={20} className="shrink-0 mt-0.5 text-blue-400" /> {t.warning}
                                </div>
                            </div>

                            {error && <div className="p-4 bg-red-50 text-red-600 rounded-2xl mt-4 font-bold flex items-center gap-2 text-sm border border-red-100"><AlertCircle size={20} /> {error}</div>}

                            <button disabled={isProcessing} onClick={handleConversion} className="mt-auto pt-6 w-full group">
                                <div className="w-full bg-blue-600 text-white py-6 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-2xl flex items-center justify-center gap-3 group-disabled:opacity-50 group-hover:bg-blue-700 group-hover:shadow-blue-200 transition-all group-active:scale-[0.98]">
                                    {isProcessing ? <RefreshCw size={20} className="animate-spin" /> : <>{t.convert_btn} <ArrowRight size={18} className="group-hover:translate-x-1 transition-transform"/></>}
                                </div>
                            </button>
                        </div>
                    )}

                    {step === 4 && (
                        <div className="flex-1 flex flex-col items-center justify-center text-center py-10 animate-in zoom-in-95 duration-500">
                            <div className="w-28 h-28 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-8 shadow-inner border border-green-100"><CheckCircle2 size={56} /></div>
                            <h2 className="text-4xl font-black mb-3 tracking-tight text-slate-800">{t.success_title}</h2>
                            <p className="text-slate-500 mb-10 font-medium max-w-sm">{t.success_desc}</p>
                            <div className="w-full flex flex-col md:flex-row gap-4 max-w-md">
                                <a href={result?.downloadUrl} download={result?.fileName} className="flex-1 bg-blue-600 text-white py-6 rounded-3xl font-black text-xs uppercase tracking-widest shadow-xl flex items-center justify-center gap-3 hover:-translate-y-1 transition-all"><Download size={20} /> {t.download_btn}</a>
                                <button onClick={() => { setStep(1); setFile(null); setVehicleName(''); setResult(null); }} className="flex-1 bg-slate-100 text-slate-500 py-6 rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-colors">{t.convert_another}</button>
                            </div>
                        </div>
                    )}
                </div>
            </main>

            <footer className="mt-auto opacity-30 text-[10px] font-black uppercase tracking-widest text-center px-4 pb-10 text-slate-500 flex flex-col items-center gap-4">
                <span>{t.footer}</span>
                <div className="flex gap-4">
                    <a href="https://github.com/Programistyk/FuelioImport" target="_blank" rel="noopener" className="hover:text-blue-600 flex items-center gap-1"><ExternalLink size={10} /> GitHub</a>
                    <a href="https://www.fuel.io/" target="_blank" rel="noopener" className="hover:text-blue-600 flex items-center gap-1"><ExternalLink size={10} /> Fuelio</a>
                </div>
            </footer>

            <style>{`
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
      `}</style>
        </div>
    );
}