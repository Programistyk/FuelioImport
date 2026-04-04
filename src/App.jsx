import React, { useState, useEffect } from 'react';
import {
    Upload, ArrowRight, CheckCircle2, AlertCircle,
    Fuel, FileText, Download, History,
    Smartphone, FileArchive, RefreshCw, Car, Sliders, ChevronLeft,
    HelpCircle, ExternalLink
} from 'lucide-react';

// --- CONFIG & TRANSLATIONS ---

const getMaxSizeMB = () => {
    try {
        const envSize = (typeof process !== 'undefined' && process.env?.VITE_MAX_FILE_SIZE_MB) ||
            (typeof import.meta !== 'undefined' && import.meta.env?.VITE_MAX_FILE_SIZE_MB);
        return envSize ? parseInt(envSize) : 20;
    } catch (e) { return 20; }
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
        step1_desc: "Wybierz format źródłowy pliku eksportu.",
        step2_back: "Wróć",
        step2_title: "Prześlij plik {name}",
        step2_desc: "Wyślij plik do przetworzenia przez serwer.",
        drop_zone: "Kliknij lub przeciągnij plik {ext}",
        browse: "Wybierz plik",
        step3_title: "Konfiguracja importu",
        step3_desc: "Dostosuj parametry przed wysłaniem na serwer.",
        vehicle_name: "Nazwa pojazdu (opcjonalnie)",
        vehicle_placeholder: "np. Mój Volkswagen",
        converter_options: "Opcje specyficzne dla formatu",
        warning: "Fuelio obsługuje tylko jeden pojazd na plik. Dane zostaną scalone w jeden profil.",
        convert_btn: "Rozpocznij konwersję",
        converting: "Przetwarzanie...",
        success_title: "Konwersja zakończona!",
        success_desc: "Twój plik został poprawnie przetworzony przez serwer.",
        download_btn: "Pobierz plik Fuelio",
        convert_another: "Konwertuj inny plik",
        footer: "© 2026 FuelioImport. Projekt Open Source.",
        error_server: "Błąd serwera podczas przetwarzania.",
        error_connection: "Nie udało się połączyć z serwerem.",
        error_empty: "Serwer nie zwrócił danych.",
        error_too_large: "Plik jest zbyt duży (Max {size}MB).",
        label_vehicle_id: "ID Eksportowanego pojazdu",
        label_dist_unit: "Jednostka dystansu",
        label_fuel_unit: "Jednostka paliwa",
        unit_km: "Kilometry (km)",
        unit_mi: "Mile (mi)",
        unit_l: "Litry (l)",
        unit_gal_us: "Galony (US)",
        unit_gal_uk: "Galony (UK)",
        help: "Pomoc"
    },
    de: {
        title: "FuelioImport",
        subtitle: "Datenkonverter",
        pwa_install: "App installieren",
        plus_circle: "Hinzufügen",
        step1_title: "Von welcher App migrieren Sie?",
        step1_desc: "Wählen Sie das Quellformat Ihrer Exportdatei.",
        step2_back: "Zurück",
        step2_title: "{name}-Datei hochladen",
        step2_desc: "Datei zur Verarbeitung an den Server senden.",
        drop_zone: "Hier klicken oder {ext}-Datei herziehen",
        browse: "Datei auswählen",
        step3_title: "Konfiguration",
        step3_desc: "Passen Sie die Parameter vor dem Senden an.",
        vehicle_name: "Fahrzeugname",
        vehicle_placeholder: "z.B. Mein Volkswagen",
        converter_options: "Konverter-Optionen",
        warning: "Fuelio unterstützt nur ein Fahrzeug pro Datei.",
        convert_btn: "Konvertierung starten",
        converting: "Verarbeitung...",
        success_title: "Abgeschlossen!",
        success_desc: "Ihre Datei wurde erfolgreich vom Server verarbeitet.",
        download_btn: "Datei herunterladen",
        convert_another: "Weitere Datei",
        footer: "© 2026 FuelioImport. Open-Source-Projekt.",
        error_server: "Serverfehler bei der Verarbeitung.",
        error_connection: "Verbindung fehlgeschlagen.",
        error_empty: "Keine Daten empfangen.",
        error_too_large: "Datei zu groß (Max {size}MB).",
        label_vehicle_id: "Fahrzeug-ID",
        label_dist_unit: "Entfernungseinheit",
        label_fuel_unit: "Kraftstoffeinheit",
        unit_km: "Kilometer (km)",
        unit_mi: "Meilen (mi)",
        unit_l: "Liter (l)",
        unit_gal_us: "Gallonen (US)",
        unit_gal_uk: "Gallonen (UK)",
        help: "Hilfe"
    },
    en: {
        title: "FuelioImport",
        subtitle: "Data converter",
        pwa_install: "Install App",
        plus_circle: "Add",
        step1_title: "Source Application?",
        step1_desc: "Select the source format of your export file.",
        step2_back: "Back",
        step2_title: "Upload {name} file",
        step2_desc: "Send file to server for processing.",
        drop_zone: "Click or drag {ext} file",
        browse: "Choose File",
        step3_title: "Import configuration",
        step3_desc: "Adjust parameters before sending to server.",
        vehicle_name: "Vehicle name",
        vehicle_placeholder: "e.g. My Volkswagen",
        converter_options: "Format specific options",
        warning: "Fuelio supports one vehicle per file. Data will be merged.",
        convert_btn: "Start conversion",
        converting: "Processing...",
        success_title: "Success!",
        success_desc: "Your file has been successfully processed by the server.",
        download_btn: "Download Fuelio file",
        convert_another: "Convert another",
        footer: "© 2026 FuelioImport. Open Source Project.",
        error_server: "Server error during processing.",
        error_connection: "Could not connect to server.",
        error_empty: "Server returned no data.",
        error_too_large: "File too large (Max {size}MB).",
        label_vehicle_id: "Vehicle ID",
        label_dist_unit: "Distance unit",
        label_fuel_unit: "Fuel unit",
        unit_km: "Kilometers (km)",
        unit_mi: "Miles (mi)",
        unit_l: "Liters (l)",
        unit_gal_us: "Gallons (US)",
        unit_gal_uk: "Gallons (UK)",
        help: "Help"
    }
};

// --- SUB-COMPONENTS ---

const LanguageSelector = ({ current, setLang }) => (
    <nav className="flex items-center bg-white p-1 rounded-xl border border-slate-200 shadow-sm shrink-0">
        {['pl', 'de', 'en'].map(l => (
            <button
                key={l}
                onClick={() => setLang(l)}
                className={`px-3 py-1.5 rounded-lg text-[10px] font-black transition-all ${current === l ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-50'}`}
            >
                {l.toUpperCase()}
            </button>
        ))}
    </nav>
);

const Header = ({ t, lang, setLang }) => (
    <header className="w-full max-w-6xl px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div className="flex items-center gap-4">
            <div className="p-3 bg-blue-600 rounded-xl shadow-lg text-white transform -rotate-2"><Fuel size={24} /></div>
            <div>
                <h1 className="text-2xl font-black tracking-tight leading-none">{t.title}</h1>
                <p className="text-[10px] text-slate-400 font-black uppercase tracking-wider">{t.subtitle}</p>
            </div>
        </div>
        <LanguageSelector current={lang} setLang={setLang} />
    </header>
);

const Footer = ({ t }) => (
    <footer className="mt-auto py-10 flex flex-col items-center gap-4 text-slate-400">
        <span className="text-[10px] font-black uppercase tracking-widest">{t.footer}</span>
        <div className="flex gap-6 text-[10px] font-black uppercase tracking-widest">
            <a href="https://github.com/Programistyk/FuelioImport" target="_blank" rel="noopener noreferrer" className="hover:text-blue-600 flex items-center gap-1 transition-colors"><ExternalLink size={10} /> GitHub</a>
            <a href="https://www.fuel.io/" target="_blank" rel="noopener noreferrer" className="hover:text-blue-600 flex items-center gap-1 transition-colors"><ExternalLink size={10} /> Fuelio</a>
        </div>
    </footer>
);

// --- MAIN APP ---

export default function App() {
    const [lang, setLang] = useState(() => {
        if (typeof navigator === 'undefined') return 'en';
        const browserLang = navigator.language.split('-')[0];
        return TRANSLATIONS[browserLang] ? browserLang : 'en';
    });

    const t = TRANSLATIONS[lang];

    const [step, setStep] = useState(1);
    const [selectedFormat, setSelectedFormat] = useState(null);
    const [file, setFile] = useState(null);
    const [vehicleName, setVehicleName] = useState('');
    const [converterSettings, setConverterSettings] = useState({});
    const [isProcessing, setIsProcessing] = useState(false);
    const [result, setResult] = useState(null);
    const [errorKey, setErrorKey] = useState(null); // Zmiana: przechowujemy klucz, nie string
    const [deferredPrompt, setDeferredPrompt] = useState(null);

    const FORMATS = [
        { id: 'acar', name: 'aCar', ext: '.abp', prefix: 'acar', Icon: FileArchive, color: 'bg-purple-600', settings: [{ key: 'vehicle_id', label: t.label_vehicle_id, type: 'number', min: 1, default: 1 }] },
        { id: 'fuellog', name: 'Fuel Log', ext: '.csv', prefix: 'fuellog', Icon: History, color: 'bg-green-500', settings: [{ key: 'vehicle_id', label: t.label_vehicle_id, type: 'number', min: 1, default: 1 }] },
        { id: 'drivvo', name: 'Drivvo', ext: '.csv', prefix: 'drivvo', Icon: Car, color: 'bg-orange-500', settings: [
                { key: 'dist_unit', label: t.label_dist_unit, type: 'select', options: [{v: 0, l: t.unit_km}, {v: 1, l: t.unit_mi}], default: 0 },
                { key: 'fuel_unit', label: t.label_fuel_unit, type: 'select', options: [{v: 0, l: t.unit_l}, {v: 1, l: t.unit_gal_us}, {v: 2, l: t.unit_gal_uk}], default: 0 }
            ]},
        { id: 'motostat', name: 'Motostat', ext: '.csv', prefix: null, Icon: FileText, color: 'bg-blue-500', settings: [] },
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
        setErrorKey(null);
    };

    const handleFileChange = (e) => {
        const selectedFile = e.target.files[0];
        if (!selectedFile) return;
        if (selectedFile.size > MAX_FILE_SIZE_BYTES) {
            setErrorKey('error_too_large');
            return;
        }
        setFile(selectedFile);
        setStep(3);
        setErrorKey(null);
    };

    const handleConversion = async () => {
        setIsProcessing(true);
        setErrorKey(null);
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
            if (!response.ok) throw new Error('error_server');

            let serverFileName = `fuelio_export_${selectedFormat.id}.csv`;
            const contentDisposition = response.headers.get('Content-Disposition');
            if (contentDisposition) {
                const fileNameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (fileNameMatch && fileNameMatch[1]) {
                    serverFileName = fileNameMatch[1].replace(/['"]/g, '');
                }
            }

            const blob = await response.blob();
            if (blob.size === 0) throw new Error('error_empty');

            setResult({ downloadUrl: URL.createObjectURL(blob), fileName: serverFileName });
            setStep(4);
        } catch (e) {
            setErrorKey(e.message.startsWith('error_') ? e.message : 'error_connection');
        } finally {
            setIsProcessing(false);
        }
    };

    // Helper do renderowania sformatowanego błędu z tłumaczeń
    const renderError = () => {
        if (!errorKey) return null;
        let message = t[errorKey] || errorKey;
        if (errorKey === 'error_too_large') {
            message = message.replace('{size}', MAX_FILE_SIZE_MB);
        }
        return (
            <div className="p-3 bg-red-50 text-red-600 rounded-xl mb-4 font-bold flex items-center gap-2 text-xs animate-in fade-in">
                <AlertCircle size={16} /> {message}
            </div>
        );
    };

    return (
        <div className="min-h-screen bg-slate-50 flex flex-col items-center font-sans antialiased text-slate-800">
            {deferredPrompt && (
                <div className="w-full bg-blue-600 p-2 text-white flex items-center justify-between sticky top-0 z-50">
                    <div className="flex items-center gap-2 ml-2 text-xs font-bold"><Smartphone size={14} /> {t.pwa_install}</div>
                    <button onClick={() => deferredPrompt.prompt()} className="bg-white text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase active:scale-95 transition-transform">{t.plus_circle}</button>
                </div>
            )}

            <Header t={t} lang={lang} setLang={setLang} />

            <main className="w-full max-w-4xl px-4 pb-20">
                <div className="bg-white rounded-[2rem] shadow-xl p-6 md:p-10 border border-white min-h-[500px] flex flex-col">

                    {step === 1 && (
                        <div className="animate-in fade-in slide-in-from-bottom-2 duration-300">
                            <h2 className="text-2xl font-black mb-1">{t.step1_title}</h2>
                            <p className="text-slate-400 text-sm mb-8">{t.step1_desc}</p>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {FORMATS.map(f => (
                                    <button key={f.id} onClick={() => selectFormat(f)} className="flex items-center p-5 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-lg border-2 border-transparent hover:border-blue-100 transition-all text-left group">
                                        <div className={`${f.color} p-3 rounded-xl text-white mr-4 shadow group-hover:scale-110 transition-transform`}><f.Icon size={20} /></div>
                                        <div className="flex-1">
                                            <div className="font-black text-slate-800 leading-tight">{f.name}</div>
                                            <div className="text-[10px] text-slate-400 font-black uppercase tracking-widest">{f.ext}</div>
                                        </div>
                                        <ArrowRight size={18} className="text-slate-200 group-hover:text-blue-500 transition-colors" />
                                    </button>
                                ))}
                            </div>
                        </div>
                    )}

                    {step === 2 && (
                        <div className="flex-1 flex flex-col animate-in fade-in slide-in-from-right-2 duration-300">
                            <button onClick={() => setStep(1)} className="text-blue-600 text-[10px] font-black uppercase flex items-center gap-1 mb-6 hover:translate-x-[-2px] transition-transform"><ChevronLeft size={14} /> {t.step2_back}</button>
                            <h2 className="text-2xl font-black mb-1">{t.step2_title.replace('{name}', selectedFormat?.name)}</h2>
                            <p className="text-slate-400 text-sm mb-8">{t.step2_desc}</p>
                            {renderError()}
                            <label className="flex-1 border-2 border-dashed border-slate-100 rounded-2xl flex flex-col items-center justify-center p-10 hover:bg-slate-50 cursor-pointer group">
                                <input type="file" className="hidden" accept={selectedFormat?.ext} onChange={handleFileChange} />
                                <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-lg mb-4 group-hover:scale-105 transition-transform"><Upload size={32} /></div>
                                <div className="text-sm font-black mb-6">{t.drop_zone.replace('{ext}', selectedFormat?.ext)}</div>
                                <div className="bg-blue-600 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg">{t.browse}</div>
                            </label>
                        </div>
                    )}

                    {step === 3 && (
                        <div className="flex-1 flex flex-col animate-in fade-in slide-in-from-right-2 duration-300">
                            <button onClick={() => { setFile(null); setStep(2); }} className="text-blue-600 text-[10px] font-black uppercase mb-6 flex items-center gap-1 w-fit"><ChevronLeft size={14} /> {t.step2_back}</button>
                            <h2 className="text-2xl font-black mb-1">{t.step3_title}</h2>
                            <div className="space-y-6 mt-6 pb-4">
                                <div>
                                    <label className="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">{t.vehicle_name}</label>
                                    <div className="relative">
                                        <Car className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300" size={16} />
                                        <input type="text" value={vehicleName} onChange={(e) => setVehicleName(e.target.value)} placeholder={t.vehicle_placeholder} className="w-full bg-slate-50 border-2 border-slate-100 rounded-xl py-3 pl-10 pr-4 font-bold text-sm outline-none focus:border-blue-500 focus:bg-white transition-all" />
                                    </div>
                                </div>

                                {selectedFormat?.settings?.map(s => (
                                    <div key={s.key}>
                                        <label className="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">{s.label}</label>
                                        {s.type === 'select' ? (
                                            <div className="flex gap-2">
                                                {s.options.map(opt => (
                                                    <button key={opt.v} onClick={() => setConverterSettings({...converterSettings, [s.key]: opt.v})} className={`flex-1 px-4 py-2 rounded-xl text-[10px] font-black transition-all border-2 ${converterSettings[s.key] === opt.v ? 'bg-white border-blue-600 text-blue-600 shadow-sm' : 'bg-white border-transparent text-slate-400 hover:border-slate-100'}`}>{opt.l}</button>
                                                ))}
                                            </div>
                                        ) : (
                                            <input type="number" min={s.min} value={converterSettings[s.key] || s.default} onChange={(e) => setConverterSettings({...converterSettings, [s.key]: parseInt(e.target.value) || s.min})} className="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 font-bold text-sm outline-none focus:border-blue-500 focus:bg-white transition-all" />
                                        )}
                                    </div>
                                ))}

                                <div className="p-4 bg-blue-50 rounded-xl flex items-start gap-3 text-blue-800 text-[11px] font-bold leading-normal">
                                    <AlertCircle size={16} className="shrink-0 mt-0.5" /> {t.warning}
                                </div>
                            </div>
                            {renderError()}
                            <button disabled={isProcessing} onClick={handleConversion} className="mt-8 w-full group">
                                <div className="w-full bg-blue-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl flex items-center justify-center gap-2 group-disabled:opacity-50 group-hover:bg-blue-700 transition-all group-active:scale-[0.98]">
                                    {isProcessing ? <RefreshCw size={18} className="animate-spin" /> : <>{t.convert_btn} <ArrowRight size={16} /></>}
                                </div>
                            </button>
                        </div>
                    )}

                    {step === 4 && (
                        <div className="flex-1 flex flex-col items-center justify-center text-center animate-in zoom-in-95 duration-300">
                            <div className="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-6 shadow-inner"><CheckCircle2 size={40} /></div>
                            <h2 className="text-2xl font-black mb-1">{t.success_title}</h2>
                            <p className="text-slate-400 text-sm mb-10 max-w-xs">{t.success_desc}</p>
                            <div className="w-full flex flex-col sm:flex-row gap-3 max-w-sm">
                                <a href={result?.downloadUrl} download={result?.fileName} className="flex-1 bg-blue-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl flex items-center justify-center gap-2 hover:-translate-y-1 transition-all"><Download size={16} /> {t.download_btn}</a>
                                <button onClick={() => { setStep(1); setFile(null); setVehicleName(''); setResult(null); setErrorKey(null); }} className="flex-1 bg-slate-100 text-slate-500 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-colors">{t.convert_another}</button>
                            </div>
                        </div>
                    )}

                </div>
            </main>

            <Footer t={t} />

            <style>{`
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slide-in-from-bottom-2 { from { transform: translateY(0.5rem); } to { transform: translateY(0); } }
        @keyframes slide-in-from-right-2 { from { transform: translateX(0.5rem); } to { transform: translateX(0); } }
        @keyframes zoom-in-95 { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .animate-in { animation-duration: 300ms; animation-fill-mode: both; }
        .fade-in { animation-name: fade-in; }
        .slide-in-from-bottom-2 { animation-name: slide-in-from-bottom-2; }
        .slide-in-from-right-2 { animation-name: slide-in-from-right-2; }
        .zoom-in-95 { animation-name: zoom-in-95; }
      `}</style>
        </div>
    );
}