import { GoogleMap, LoadScript, Marker, Autocomplete } from "@react-google-maps/api";
import { useState, useEffect, useRef, ChangeEvent } from "react";
import { ToastContainer, toast } from 'react-toastify';

const GOOGLE_MAPS_API_KEY: string = "AIzaSyCQwQBdz0VW-SupCGwD3gE7niI3uxaw1dc"; // Remplace par ton API Key

const containerStyle: React.CSSProperties = {
    width: "100%",
    height: "500px",
};

interface Position {
    lat: number;
    lng: number;
}

interface SignalProps {
    quartier?: string;
}
interface QuartierType {
    id: number;
    nom: number;
    lat: string;
    lng: string;
}

interface DataType {
    categorie: string;
    description: string;
    quartier: string;
    contact: string;
    photo: string | null | File;
}

const MapComponent: React.FC = ({ quartier }: SignalProps) => {
    const [position, setPosition] = useState<Position>();
    const [formData, setFormData] = useState<DataType>({
        categorie: "",
        description: "",
        quartier: "",
        contact: "",
        photo: null,
    });
    const [map, setMap] = useState<google.maps.Map | null>(null);
    const [isSend, setSending] = useState(false);
    const autocompleteRef = useRef<google.maps.places.Autocomplete | null>(null);
    const quartiers: QuartierType[] = quartier === undefined ? [] : JSON.parse(quartier) as QuartierType[];
    const Categories = [
        'Le conteneur est plein',
        'Déchets dans la rue',
        "Déchets à l'extérieur du conteneur",
        "Poubelle informelle",
        "Conteneur en feu",
        "Des ordures dans le fossé",

    ]

    useEffect(() => {
        if (navigator.geolocation) {
            console.log("test")
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    setPosition({ lat: pos.coords.latitude, lng: pos.coords.longitude });
                },
                () => alert("Impossible d'obtenir votre position")
            );
        }
    }, []);

    // Déplacer le marqueur au clic
    console.log(formData)
    const handleMapClick = (event: google.maps.MapMouseEvent) => {
        if (event.latLng) {
            setPosition({ lat: event.latLng.lat(), lng: event.latLng.lng() });
            map?.panTo(event.latLng);
        }
    };

    if (position === null) {
        return
    }
    return (
        <div className="row">
            <div className="col-sm-6">
                <LoadScript googleMapsApiKey={GOOGLE_MAPS_API_KEY} libraries={["places"]}>
                    <GoogleMap
                        mapContainerStyle={containerStyle}
                        center={position}

                        zoom={13}
                        onLoad={(map) => setMap(map)}
                        onClick={handleMapClick}
                    >
                        {position && <Marker position={position} />}
                    </GoogleMap>
                </LoadScript>
            </div>
            <div className="col-sm-6">
                <div className="card-body">
                    <form onSubmit={(ev) => {
                        ev.preventDefault();
                        let data = new FormData();
                        setSending(old=>true)
                        data.append("categorie", formData.categorie);
                        data.append("description", formData.description);
                        data.append("quartier", formData.quartier);
                        data.append("contact", formData.contact);
                        data.append("photo", formData.photo as File);
                        data.append("lat", position?.lat.toString() as string);
                        data.append("lng", position?.lng.toString() as string);

                        fetch("/api/signal", {
                            method: "POST",
                            body: data,
                        }).then(d => d.json()).then(d => {
                            setSending(old=>false)
                            if (d.code === 200) {
                                toast.success("Signalement envoyé avec succès, ID: " + d.id);
                                setFormData({
                                    categorie: "",
                                    description: "",
                                    quartier: "",
                                    contact: "",
                                    photo: null,
                                });
                            } else {
                                toast.error("Erreur lors de l'envoi du signalement");
                            }
                        })
                    }} >
                        <div className="row">
                            <div className="col-md-12">
                                <div className="mb-3">
                                    <label className="form-label">Catégorie</label>
                                    <select
                                        className="form-select"
                                        value={formData.categorie}
                                        onChange={(e: ChangeEvent<HTMLSelectElement>) => {
                                            setFormData((old) => ({ ...old, categorie: e.target.value }));
                                        }}
                                    >
                                        <option value="">Sélectionner une catégorie</option>
                                        {Categories.map((cat) => (
                                            <option key={cat} value={cat}>
                                                {cat}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Quartier</label>
                                    <select className="form-select"
                                        onChange={(e: ChangeEvent<HTMLSelectElement>) => {
                                            setFormData((old) => ({ ...old, quartier: e.target.value }));

                                            let current_qt = quartiers.find((q: any) => q.id === parseInt(e.target.value))
                                            if (current_qt) {
                                                setPosition({ lat: parseFloat(current_qt.lat), lng: parseFloat(current_qt.lng) })
                                                map?.panTo({ lat: parseFloat(current_qt.lat), lng: parseFloat(current_qt.lng) });
                                            }

                                        }}
                                    >
                                        <option value="">Sélectionner un quartier</option>
                                        {quartiers.map((q: any) => (
                                            <option key={q.id} value={q.id}>{q.nom}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Contact</label>
                                    <input type="tel" value={formData.contact} className="form-control"
                                        onChange={(e: ChangeEvent<HTMLInputElement>) => {
                                            setFormData((old) => ({ ...old, contact: e.target.value }));
                                        }
                                        }
                                    />
                                </div>
                            </div>
                            <div className="col-md-12">
                                <div className="mb-3">
                                    <label className="form-label">Photo</label>
                                    <input
                                        onChange={(e: ChangeEvent<HTMLInputElement>) => {
                                            if (e.target.files && e.target.files.length > 0) {
                                                const file = e.target.files[0];
                                                setFormData((old) => ({ ...old, photo: file }));
                                                console.log("Fichier sélectionné :", file.name);
                                            }
                                        }}
                                        type="file" className="form-control" accept="image/*" />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Description</label>
                                    <textarea className="form-control" rows={3}
                                        onChange={(e: ChangeEvent<HTMLTextAreaElement>) => {
                                            setFormData((old) => ({ ...old, description: e.target.value }));
                                        }}
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        <div className="text-end mt-3">
                            <button type="submit" className="btn"
                                style={{
                                    border: 0,
                                    background: "var(--accent-color)",
                                    color: "var(--contrast-color)",
                                    transition: "0.3s",
                                    borderRadius: 8
                                }}

                                disabled={formData.categorie === "" || formData.quartier === "" || formData.contact === "" || isSend === true}
                            >
                                {isSend ? "Envoi en cours..." : "Signaler"}
                            </button>
                        </div>
                    </form>
                </div>
            </div >
            <ToastContainer />
        </div >
    );
};

export default MapComponent;
