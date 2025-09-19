# Layout- und Komponenten-Übersicht

## Allgemeine Struktur (nach Laravel-Konventionen)

Die Laravel-Dokumentation empfiehlt, dass Views in `resources/views` organisiert werden. 
- Blade-Komponenten sollten im Verzeichnis `resources/views/components` liegen
- Für Layouts gibt es verschiedene Ansätze: Verwendung von Blade-Komponenten (`<x-layouts.*>`) oder Template-Vererbung (@extends)

## Neue Layout-Architektur

Die bisherigen separaten Layouts wurden in eine einheitliche parametrisierbare Komponente zusammengefasst:

| Komponente/Layout          | Definiert in                           | Parameter | Verwendungszweck |
|---------------------------|----------------------------------------|-----------|------------------|
| `<x-layouts.main>`         | `components/layouts/main.blade.php`     | `type`: 'auth' oder 'app'<br>`variant`: 'card', 'simple', 'split' (für auth) oder 'header', 'sidebar' (für app)<br>`title`: Seitentitel | Einheitliches Layout für alle Seiten |

### Verwendungsbeispiele

```blade
<!-- Für Login-Seiten (ehemals auth/card) -->
<x-layouts.main type="auth" variant="card" title="Login">
    <!-- Inhalt -->
</x-layouts.main>

<!-- Für Registrierungsseiten (ehemals auth/simple) -->
<x-layouts.main type="auth" variant="simple" title="Registrieren">
    <!-- Inhalt -->
</x-layouts.main>

<!-- Für Dashboard mit Header (ehemals app/header) -->
<x-layouts.main type="app" variant="header" title="Dashboard">
    <!-- Inhalt -->
</x-layouts.main>

<!-- Für Dashboard mit Sidebar (ehemals app/sidebar) -->
<x-layouts.main type="app" variant="sidebar" title="Dashboard">
    <!-- Inhalt -->
</x-layouts.main>
```

## Partials

Die gemeinsamen Layout-Elemente wurden in Partials ausgelagert:

| Partial                 | Definiert in                         | Parameter | Verwendungszweck |
|------------------------|-------------------------------------|-----------|------------------|
| `@include('partials.head')` | `partials/head.blade.php`           | `title`: Seitentitel | Meta-Tags, Scripts, CSS |
| `@include('partials.navigation')` | `partials/navigation.blade.php` | `variant`: 'header' oder 'sidebar' | Navigation-Links |
| `@include('partials.user-dropdown')` | `partials/user-dropdown.blade.php` | `position`, `align`, `showName` | Benutzer-Dropdown |
| `@include('partials.footer')` | `partials/footer.blade.php`       | - | Scripts am Ende der Seite |

## UI-Komponenten

### Brand-Komponente

Die `app-logo` und `app-logo-icon` Komponenten wurden in eine einheitliche Brand-Komponente konsolidiert:

```blade
<!-- Logo mit Text -->
<x-ui.brand showText="true" variant="default" size="md" />

<!-- Nur Icon -->
<x-ui.brand :showText="false" variant="auth" size="sm" />

<!-- Großes Logo im Header -->
<x-ui.brand variant="header" size="lg" textClass="text-white" />
```

### Formular-Komponenten

Die Formular-Komponenten wurden in den UI-Namespace verschoben und mit konsistentem Styling aktualisiert:

| Alte Komponente          | Neue Komponente           | Parameter |
|-------------------------|--------------------------|-----------|
| `<x-input-label>`        | `<x-ui.label>`            | `for`: ID des zugehörigen Formularelements |
| `<x-input-error>`        | `<x-ui.input-error>`      | `messages`: Fehlermeldungen |
| `<x-text-area>`          | `<x-ui.textarea>`         | `disabled`: Deaktiviert das Feld |
| `<x-text-input>`         | `<x-ui.input>`            | `type`: Input-Typ (text, email, password...)<br>`disabled`: Deaktiviert das Feld |

Beispiel für ein Formularfeld:

```blade
<div>
    <x-ui.label for="email" :value="__('Email')" />
    <x-ui.input id="email" type="email" name="email" :value="old('email')" required autofocus />
    <x-ui.input-error :messages="$errors->get('email')" />
</div>
```

### Status-Komponente

Die `action-message` und `auth-session-status` wurden in eine neue Status-Komponente konsolidiert:

```blade
<!-- Erfolgs-Status (für Livewire) -->
<x-ui.status type="success" on="profile-updated">
    {{ __('Saved.') }}
</x-ui.status>

<!-- Fehler-Status -->
<x-ui.status type="error" :status="session('error')"></x-ui.status>

<!-- Info-Status mit Auto-Hide -->
<x-ui.status type="info" autoHide hideDelay="5000" dismissable>
    {{ __('This message will disappear in 5 seconds.') }}
</x-ui.status>

<!-- Warnungs-Status -->
<x-ui.status type="warning" dismissable>
    {{ __('Warning: This action cannot be undone.') }}
</x-ui.status>
```

## Button-Komponenten 

Die Button-Komponenten sind unverändert und werden weiterhin verwendet:

```blade
<x-ui.button>{{ __('Save') }}</x-ui.button>
<x-ui.link-button href="{{ route('home') }}">{{ __('Back') }}</x-ui.link-button>
```

## Migration zu den neuen Komponenten

### Schritt 1: Layouts aktualisieren

Ersetze alte Layout-Komponenten:

```diff
- <x-layouts.auth.card>
+ <x-layouts.main type="auth" variant="card">
    <!-- Inhalt -->
- </x-layouts.auth.card>
+ </x-layouts.main>
```

### Schritt 2: Formular-Komponenten aktualisieren

Ersetze alte Formular-Komponenten:

```diff
- <x-input-label for="name" :value="__('Name')" />
- <x-text-input id="name" type="text" name="name" :value="old('name')" required />
- <x-input-error :messages="$errors->get('name')" />
+ <x-ui.label for="name" :value="__('Name')" />
+ <x-ui.input id="name" type="text" name="name" :value="old('name')" required />
+ <x-ui.input-error :messages="$errors->get('name')" />
```

### Schritt 3: Brand-Komponente verwenden

Ersetze Logo-Komponenten:

```diff
- <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
+ <x-ui.brand :showText="false" variant="auth" iconClass="size-9 fill-current text-black dark:text-white" />
```

### Schritt 4: Status-Komponente verwenden

Ersetze Status-Meldungen:

```diff
- <x-action-message on="profile-updated" class="text-green-500" />
+ <x-ui.status type="success" on="profile-updated" />

- <x-auth-session-status :status="session('status')" />
+ <x-ui.status :status="session('status')" />
```
