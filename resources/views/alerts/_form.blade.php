@php($editing = isset($alert))

<label>Type de catastrophe
    <select name="disaster_id" required>
        <option value="">Sélectionner un type</option>
        @foreach($disasters->groupBy(fn($d) => $d->category?->name ?? 'Autres') as $category => $items)
            <optgroup label="{{ $category }}">
                @foreach($items as $d)
                    <option value="{{ $d->id }}" @selected((string) old('disaster_id', $editing ? $alert->disaster_id : '') === (string) $d->id)>
                        {{ $d->name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</label>

<label>Description de la situation
    <textarea name="description" rows="6" required minlength="20" maxlength="3000" placeholder="Décrivez simplement ce que vous voyez, ce qui se passe et les personnes ou lieux concernés...">{{ old('description', $editing ? $alert->description : '') }}</textarea>
    <small>Restez factuel : indiquez simplement ce que vous observez. Aucun niveau d’urgence n’est à choisir.</small>
</label>

<div class="location-block">
    <div class="location-block-head">
        <div><strong><i class="fa-solid fa-location-crosshairs"></i> Localisation</strong><span>Utilisez la position du téléphone ou saisissez les coordonnées.</span></div>
        <button class="button button-secondary button-sm" type="button" data-geolocate><i class="fa-solid fa-location-dot"></i> Utiliser ma position</button>
    </div>
    <div class="form-grid two">
        <label>Latitude<input id="latitude" type="number" step="0.00000001" min="-90" max="90" name="latitude" value="{{ old('latitude', $editing ? $alert->latitude : '') }}" required inputmode="decimal"></label>
        <label>Longitude<input id="longitude" type="number" step="0.00000001" min="-180" max="180" name="longitude" value="{{ old('longitude', $editing ? $alert->longitude : '') }}" required inputmode="decimal"></label>
    </div>
    <span class="help-text" data-geolocation-status aria-live="polite">Votre navigateur demandera votre autorisation avant d’accéder à la position.</span>
</div>

<label>Adresse ou repère <span class="field-optional">optionnel</span>
    <input type="text" name="address" maxlength="255" value="{{ old('address', $editing ? $alert->address : '') }}" placeholder="Quartier, rue, bâtiment, marché, pont, repère...">
</label>

<div class="photo-uploader" data-photo-uploader data-existing-count="{{ $editing ? $alert->photos->count() : 0 }}">
    <div class="photo-uploader-heading">
        <div><strong><i class="fa-solid fa-images"></i> Photos de la catastrophe <span class="field-optional">optionnel</span></strong><span>Jusqu’à 6 photos · JPG, PNG ou WebP · 4 Mo maximum par photo.</span></div>
        <span class="photo-total" data-photo-count>{{ $editing ? $alert->photos->count() : 0 }}/6</span>
    </div>

    <div class="photo-source-grid">
        <label class="photo-source-card" for="camera-photo">
            <span class="photo-source-icon camera"><i class="fa-solid fa-camera"></i></span>
            <span><strong>Prendre une photo</strong><small>Ouvre directement la caméra sur un téléphone compatible.</small></span>
            <input id="camera-photo" class="sr-only" type="file" name="camera_photo" accept="image/*" capture="environment" data-camera-input>
        </label>

        <label class="photo-source-card" for="gallery-photos">
            <span class="photo-source-icon gallery"><i class="fa-regular fa-images"></i></span>
            <span><strong>Choisir dans la galerie</strong><small>Sélectionnez une ou plusieurs photos déjà présentes sur l’appareil.</small></span>
            <input id="gallery-photos" class="sr-only" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple data-gallery-input>
        </label>
    </div>

    <p class="photo-uploader-status" data-photo-status>Aucune nouvelle photo sélectionnée.</p>
    <div class="photo-preview-grid" data-photo-preview aria-live="polite"></div>
</div>

@if($editing && $alert->photos->isNotEmpty())
    <div class="existing-evidence">
        <strong>Photos déjà enregistrées ({{ $alert->photos->count() }}/6)</strong>
        <div class="photo-grid">
            @foreach($alert->photos as $photo)
                <div class="photo-thumb">
                    <img src="{{ route('alert.photos.show', $photo) }}" alt="Photo du signalement" loading="lazy">
                    <form method="POST" action="{{ route('alerts.photos.destroy', [$alert, $photo]) }}" data-confirm="Supprimer cette photo ?">
                        @csrf @method('DELETE')
                        <button class="photo-delete" type="submit" aria-label="Supprimer cette photo"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif
