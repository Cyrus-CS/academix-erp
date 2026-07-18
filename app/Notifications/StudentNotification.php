<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification générique pour les événements liés à un élève.
 *
 * Usage :
 *   // Création d'élève
 *   $user->notify(new StudentNotification($student, 'created'));
 *
 *   // Mise à jour
 *   $user->notify(new StudentNotification($student, 'updated'));
 *
 *   // Note ajoutée
 *   $user->notify(new StudentNotification($student, 'grade_added', [
 *       'subject' => 'Mathématiques',
 *       'score'   => 15,
 *   ]));
 *
 *   // Absence
 *   $user->notify(new StudentNotification($student, 'absent', [
 *       'date'  => '2025-01-15',
 *       'class' => '6ème A',
 *   ]));
 *
 *   // Paiement reçu
 *   $user->notify(new StudentNotification($student, 'payment_received', [
 *       'amount'   => 50000,
 *       'fee_type' => 'Scolarité T1',
 *   ]));
 */
class StudentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Types d'événements supportés.
     */
    public const TYPE_CREATED          = 'created';
    public const TYPE_UPDATED          = 'updated';
    public const TYPE_GRADE_ADDED      = 'grade_added';
    public const TYPE_ABSENT           = 'absent';
    public const TYPE_LATE             = 'late';
    public const TYPE_PAYMENT_RECEIVED = 'payment_received';
    public const TYPE_PAYMENT_OVERDUE  = 'payment_overdue';
    public const TYPE_REPORT_READY     = 'report_ready';

    /**
     * @param  Student  $student  L'élève concerné
     * @param  string   $event    Type d'événement (voir constantes ci-dessus)
     * @param  array    $extra    Données supplémentaires selon l'événement
     */
    public function __construct(
        public readonly Student $student,
        public readonly string  $event  = self::TYPE_CREATED,
        public readonly array   $extra  = [],
    ) {}

    // ────────────────────────────────────────────────────────────
    // Canaux de diffusion
    // ────────────────────────────────────────────────────────────

    /**
     * Canaux utilisés : base de données + email.
     * Ajoutez 'vonage' ou 'twilio' pour les SMS si nécessaire.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Email uniquement si l'utilisateur a un email vérifié
        if ($notifiable->email && $notifiable->hasVerifiedEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    // ────────────────────────────────────────────────────────────
    // Canal : Base de données
    // ────────────────────────────────────────────────────────────

    /**
     * Données stockées dans la table notifications.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'event'       => $this->event,
            'student_id'  => $this->student->id,
            'student_name'=> $this->student->user->name ?? 'Élève',
            'message'     => $this->_buildMessage(),
            'icon'        => $this->_getIcon(),
            'color'       => $this->_getColor(),
            'url'         => route('students.show', $this->student),
            'extra'       => $this->extra,
        ];
    }

    // ────────────────────────────────────────────────────────────
    // Canal : Email
    // ────────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->student->user->name ?? 'l\'élève';
        $url         = route('students.show', $this->student);

        return (new MailMessage)
            ->subject($this->_buildSubject())
            ->greeting("Bonjour {$studentName},")
            ->line($this->_buildMessage())
            ->action('Voir le profil de l\'élève', $url)
            ->line('Merci d\'utiliser School ERP.')
            ->salutation('Cordialement, l\'équipe School ERP');
    }

    // ────────────────────────────────────────────────────────────
    // Helpers privés
    // ────────────────────────────────────────────────────────────

    /**
     * Construit le message selon l'événement.
     */
    private function _buildMessage(): string
    {
        $name = $this->student->user->name ?? 'L\'élève';

        return match ($this->event) {

            self::TYPE_CREATED =>
                "{$name} a été inscrit(e) avec succès.",

            self::TYPE_UPDATED =>
                "Le profil de {$name} a été mis à jour.",

            self::TYPE_GRADE_ADDED =>
                sprintf(
                    "%s a obtenu %s/20 en %s.",
                    $name,
                    $this->extra['score']   ?? '—',
                    $this->extra['subject'] ?? 'une matière',
                ),

            self::TYPE_ABSENT =>
                sprintf(
                    "%s était absent(e) le %s%s.",
                    $name,
                    $this->extra['date']  ?? 'aujourd\'hui',
                    isset($this->extra['class']) ? " en {$this->extra['class']}" : '',
                ),

            self::TYPE_LATE =>
                sprintf(
                    "%s est arrivé(e) en retard le %s.",
                    $name,
                    $this->extra['date'] ?? 'aujourd\'hui',
                ),

            self::TYPE_PAYMENT_RECEIVED =>
                sprintf(
                    "Un paiement de %s FCFA a été reçu pour %s (%s).",
                    number_format((float) ($this->extra['amount'] ?? 0), 0, ',', ' '),
                    $name,
                    $this->extra['fee_type'] ?? 'frais scolaires',
                ),

            self::TYPE_PAYMENT_OVERDUE =>
                sprintf(
                    "Le paiement de %s pour %s est en retard.",
                    $this->extra['fee_type'] ?? 'frais scolaires',
                    $name,
                ),

            self::TYPE_REPORT_READY =>
                sprintf(
                    "Le bulletin de %s pour %s est disponible.",
                    $name,
                    $this->extra['term'] ?? 'ce trimestre',
                ),

            default =>
                "Un événement concernant {$name} a été enregistré.",
        };
    }

    /**
     * Sujet de l'email selon l'événement.
     */
    private function _buildSubject(): string
    {
        $name = $this->student->user->name ?? 'un élève';

        return match ($this->event) {
            self::TYPE_CREATED          => "Nouvel élève inscrit : {$name}",
            self::TYPE_UPDATED          => "Profil mis à jour : {$name}",
            self::TYPE_GRADE_ADDED      => "Nouvelle note pour {$name}",
            self::TYPE_ABSENT           => "Absence signalée : {$name}",
            self::TYPE_LATE             => "Retard signalé : {$name}",
            self::TYPE_PAYMENT_RECEIVED => "Paiement reçu pour {$name}",
            self::TYPE_PAYMENT_OVERDUE  => "Paiement en retard : {$name}",
            self::TYPE_REPORT_READY     => "Bulletin disponible : {$name}",
            default                     => "Notification School ERP : {$name}",
        };
    }

    /**
     * Icône Bootstrap selon l'événement.
     */
    private function _getIcon(): string
    {
        return match ($this->event) {
            self::TYPE_CREATED          => 'bi-person-plus-fill',
            self::TYPE_UPDATED          => 'bi-pencil-square',
            self::TYPE_GRADE_ADDED      => 'bi-star-fill',
            self::TYPE_ABSENT           => 'bi-x-circle-fill',
            self::TYPE_LATE             => 'bi-clock-fill',
            self::TYPE_PAYMENT_RECEIVED => 'bi-check-circle-fill',
            self::TYPE_PAYMENT_OVERDUE  => 'bi-exclamation-triangle-fill',
            self::TYPE_REPORT_READY     => 'bi-file-earmark-text-fill',
            default                     => 'bi-info-circle-fill',
        };
    }

    /**
     * Couleur selon l'événement (correspond à ton design system).
     */
    private function _getColor(): string
    {
        return match ($this->event) {
            self::TYPE_CREATED          => 'emerald',
            self::TYPE_UPDATED          => 'blue',
            self::TYPE_GRADE_ADDED      => 'amber',
            self::TYPE_ABSENT           => 'red',
            self::TYPE_LATE             => 'amber',
            self::TYPE_PAYMENT_RECEIVED => 'emerald',
            self::TYPE_PAYMENT_OVERDUE  => 'red',
            self::TYPE_REPORT_READY     => 'cyan',
            default                     => 'blue',
        };
    }
}