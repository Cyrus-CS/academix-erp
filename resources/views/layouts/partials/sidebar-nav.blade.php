{{-- ═══════════════════════════════════════════════════════════
     HELPERS LOCAUX
     $active(string)  → vérifie si la route courante correspond
     $hasAnyRole([])  → vérifie les rôles (Spatie)
═══════════════════════════════════════════════════════════ --}}

@php
/**
* Retourne les classes CSS d'un item actif ou inactif.
*/
$navItem = function (string|array $routes): string {
$routes = (array) $routes;
$isActive = collect($routes)->contains(
fn($r) => request()->routeIs($r)
);

return $isActive
? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30'
: 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800
dark:hover:text-slate-200';
};

$iconClass = function (string|array $routes): string {
$routes = (array) $routes;
$isActive = collect($routes)->contains(
fn($r) => request()->routeIs($r)
);

return $isActive
? 'text-white'
: 'text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400';
};

$badgeClass = function (string|array $routes): string {
$routes = (array) $routes;
$isActive = collect($routes)->contains(
fn($r) => request()->routeIs($r)
);

return $isActive
? 'bg-white/25 text-white'
: 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400';
};
@endphp

{{-- ═══════════════════════════════════════════════════════════
     1. TABLEAU DE BORD
═══════════════════════════════════════════════════════════ --}}
<a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('dashboard') }}" title="Tableau de bord">
    <i class="bi bi-grid-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('dashboard') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Tableau de bord
    </span>
</a>


{{-- ═══════════════════════════════════════════════════════════
     2. ACADÉMIQUE
═══════════════════════════════════════════════════════════ --}}
@canany(['view academic_years', 'view terms', 'view classes', 'view subjects', 'view timetables'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Académique
    </p>
</div>

{{-- Années académiques --}}
@can('view academic_years')
<a href="{{ route('academic-years.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('academic-years.*') }}" title="Années académiques">
    <i class="bi bi-calendar3 text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('academic-years.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Années académiques
    </span>
</a>
@endcan

{{-- Trimestres --}}
@can('view terms')
<a href="{{ route('terms.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('terms.*') }}" title="Trimestres">
    <i class="bi bi-calendar-range text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('terms.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Trimestres
    </span>
</a>
@endcan

{{-- Classes --}}
@can('view classes')
<a href="{{ route('classes.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('classes.*') }}" title="Classes">
    <i class="bi bi-building text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('classes.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Classes
    </span>
</a>
@endcan

{{-- Matières / Sujets --}}
@can('view subjects')
<a href="{{ route('subjects.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('subjects.*') }}" title="Matières">
    <i class="bi bi-book-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('subjects.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Matières
    </span>
</a>
@endcan

{{-- Emplois du temps --}}
@can('view timetables')
<a href="{{ route('timetables.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('timetables.*') }}" title="Emplois du temps">
    <i class="bi bi-clock-history text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('timetables.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Emplois du temps
    </span>
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     3. ÉLÈVES
═══════════════════════════════════════════════════════════ --}}
@canany(['view students', 'view parents'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Élèves
    </p>
</div>

{{-- Liste des élèves --}}
@can('view students')
<a href="{{ route('students.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('students.*') }}" title="Élèves">
    <i class="bi bi-people-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('students.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap flex-1">
        Élèves
    </span>
    {{-- Badge compteur --}}
    @php $studentCount = \App\Models\Student::count(); @endphp
    @if($studentCount > 0)
    <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full
                 {{ $badgeClass('students.*') }}">
        {{ $studentCount > 999 ? '999+' : $studentCount }}
    </span>
    @endif
</a>
@endcan

{{-- Parents --}}
@can('view parents')
<a href="{{ route('parents.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('parents.*') }}" title="Parents">
    <i class="bi bi-person-heart text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('parents.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Parents
    </span>
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     4. ENSEIGNANTS
═══════════════════════════════════════════════════════════ --}}
@canany(['view teachers', 'view teacher_contracts', 'view teacher_assignments'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Enseignants
    </p>
</div>

{{-- Liste enseignants --}}
@can('view teachers')
<a href="{{ route('teachers.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('teachers.*') }}" title="Enseignants">
    <i class="bi bi-person-badge-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('teachers.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap flex-1">
        Enseignants
    </span>
    @php $teacherCount = \App\Models\Teacher::count(); @endphp
    @if($teacherCount > 0)
    <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full
                 {{ $badgeClass('teachers.*') }}">
        {{ $teacherCount }}
    </span>
    @endif
</a>
@endcan

{{-- Contrats --}}
@can('view teacher_contracts')
<a href="{{ route('teacher-contracts.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('teacher-contracts.*') }}" title="Contrats">
    <i class="bi bi-file-earmark-text-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('teacher-contracts.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Contrats
    </span>
</a>
@endcan

{{-- Affectations --}}
@can('view teacher_assignments')
<a href="{{ route('teacher-assignments.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('teacher-assignments.*') }}" title="Affectations">
    <i class="bi bi-diagram-3-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('teacher-assignments.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Affectations
    </span>
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     5. ÉVALUATIONS
═══════════════════════════════════════════════════════════ --}}
@canany(['view attendance', 'view grades', 'view report_cards'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Évaluations
    </p>
</div>

{{-- Présences --}}
@can('view attendance')
<a href="{{ route('attendance.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('attendance.*') }}" title="Présences">
    <i class="bi bi-clipboard2-check-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('attendance.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap flex-1">
        Présences
    </span>
    {{-- Badge : absences du jour --}}
    @php
    $todayAbsences = \App\Models\Attendance::whereDate('date', today())
    ->where('status', 'absent')->count();
    @endphp
    @if($todayAbsences > 0)
    <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full
                 {{ request()->routeIs('attendance.*')
                    ? 'bg-white/25 text-white'
                    : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }}">
        {{ $todayAbsences }}
    </span>
    @endif
</a>
@endcan

{{-- Notes --}}
@can('view grades')
<a href="{{ route('grades.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('grades.*') }}" title="Notes">
    <i class="bi bi-pencil-square text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('grades.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Notes
    </span>
</a>
@endcan

{{-- Bulletins --}}
@can('view report_cards')
<a href="{{ route('report-cards.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('report-cards.*') }}" title="Bulletins">
    <i class="bi bi-file-earmark-bar-graph-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('report-cards.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Bulletins
    </span>
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     6. FINANCES
═══════════════════════════════════════════════════════════ --}}
@canany(['view fee_types', 'view payments'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Finances
    </p>
</div>

{{-- Types de frais --}}
@can('view fee_types')
<a href="{{ route('fee-types.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('fee-types.*') }}" title="Types de frais">
    <i class="bi bi-tags-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('fee-types.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Types de frais
    </span>
</a>
@endcan

{{-- Paiements --}}
@can('view payments')
<a href="{{ route('payments.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('payments.*') }}" title="Paiements">
    <i class="bi bi-cash-stack text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('payments.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap flex-1">
        Paiements
    </span>
    {{-- Badge : paiements en attente --}}
    @php
    $pendingPayments = \App\Models\Payment::where('status', 'pending')->count();
    @endphp
    @if($pendingPayments > 0)
    <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full
                 {{ request()->routeIs('payments.*')
                    ? 'bg-white/25 text-white'
                    : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
        {{ $pendingPayments }}
    </span>
    @endif
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     7. COMMUNICATION
═══════════════════════════════════════════════════════════ --}}
@canany(['view announcements', 'view notifications'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Communication
    </p>
</div>

{{-- Annonces --}}
@can('view announcements')
<a href="{{ route('announcements.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('announcements.*') }}" title="Annonces">
    <i class="bi bi-megaphone-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('announcements.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Annonces
    </span>
</a>
@endcan

{{-- Notifications --}}
@can('view notifications')
<a href="{{ route('notifications.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('notifications.*') }}" title="Notifications">
    <i class="bi bi-bell-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('notifications.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap flex-1">
        Notifications
    </span>
    @php $unread = auth()->user()?->unreadNotifications->count() ?? 0; @endphp
    @if($unread > 0)
    <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full
                 {{ request()->routeIs('notifications.*')
                    ? 'bg-white/25 text-white'
                    : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }}">
        {{ $unread > 9 ? '9+' : $unread }}
    </span>
    @endif
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     8. RAPPORTS & EXPORTS
═══════════════════════════════════════════════════════════ --}}
@canany(['view reports', 'export data'])

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Rapports
    </p>
</div>

{{-- Statistiques --}}
@can('view reports')
<a href="{{ route('reports.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('reports.*') }}" title="Statistiques">
    <i class="bi bi-bar-chart-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('reports.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Statistiques
    </span>
</a>
@endcan

{{-- Exports Excel --}}
@can('export data')
<a href="{{ route('exports.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('exports.*') }}" title="Exports Excel">
    <i class="bi bi-file-earmark-spreadsheet-fill text-base shrink-0 sidebar-icon
              transition-colors {{ $iconClass('exports.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Exports Excel
    </span>
</a>
@endcan

@endcanany


{{-- ═══════════════════════════════════════════════════════════
     9. ADMINISTRATION (admin only)
═══════════════════════════════════════════════════════════ --}}
@role('Administrateur')

<div class="pt-3 pb-1 px-1">
    <p class="sidebar-section-title text-[10px] font-semibold uppercase tracking-widest
              text-slate-400 dark:text-slate-600 px-2 whitespace-nowrap
              transition-all duration-300">
        Administration
    </p>
</div>

{{-- Utilisateurs --}}
<a href="{{ route('users.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('users.*') }}" title="Utilisateurs">
    <i class="bi bi-shield-lock-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('users.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap flex-1">
        Utilisateurs
    </span>
    @php $userCount = \App\Models\User::count(); @endphp
    <span class="sidebar-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full
                 {{ $badgeClass('users.*') }}">
        {{ $userCount }}
    </span>
</a>

{{-- Rôles & Permissions --}}
<a href="{{ route('roles.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem(['roles.*', 'permissions.*']) }}" title="Rôles & Permissions">
    <i class="bi bi-key-fill text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass(['roles.*', 'permissions.*']) }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Rôles & Permissions
    </span>
</a>

{{-- Paramètres --}}
<a href="{{ route('settings.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl
           transition-all duration-200 ripple
           {{ $navItem('settings.*') }}" title="Paramètres">
    <i class="bi bi-gear-wide-connected text-base shrink-0 sidebar-icon transition-colors
              {{ $iconClass('settings.*') }}"></i>
    <span class="text-sm font-medium sidebar-label whitespace-nowrap">
        Paramètres
    </span>
</a>

@endrole


{{-- ═══════════════════════════════════════════════════════════
     SÉPARATEUR FINAL
═══════════════════════════════════════════════════════════ --}}
<div class="pt-2"></div>