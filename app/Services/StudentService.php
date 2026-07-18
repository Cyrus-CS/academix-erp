<?php
namespace App\Services;

use App\Models\Student;
use App\Models\User;
use App\Notifications\StudentNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentService{

    public function store(array $data){
        DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make(
                    $data['password'] ?? Str::password()
                ),
            ]);

            $user->assignRole('Student');
        
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $data['class_id'],
                'academic_year_id' => $data['academic_year_id'],
                'matricule' => Student::generateMatricule($data['academic_year_id']),
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'guardian_name' => $data['guardian_name'],
                'guardian_phone' => $data['guardian_phone'],
                'address' => $data['address'],
            ]);
            $this->saveImage($student, $data);

            // ── Notification création ──────────────────────────────
            // Notifier tous les admins
            $admins = auth()->user()->hasRole('Admin');

            Notification::send(
                $admins,
                new StudentNotification($student, StudentNotification::TYPE_CREATED)
            );

            // Notifier les parents si déjà associés
            $student->parents->each(function ($parent) use ($student) {
                $parent->user->notify(
                    new StudentNotification($student, StudentNotification::TYPE_CREATED)
                );
            });
            
        });
    }

    public function update(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {

            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $student->user->update($userData);

            $student->update([
                'class_id'  => $data['class_id'],
                'academic_year_id'=> $data['academic_year_id'],
                'birth_date'      => $data['birth_date'],
                'gender'  => $data['gender'],
                'guardian_name'   => $data['guardian_name'],
                'guardian_phone'  => $data['guardian_phone'],
                'address' => $data['address'],
            ]);

            $this->saveImage($student, $data);

            // ── Notification mise à jour ───────────────────────────
            // Notifier les parents
            $student->parents->each(function ($parent) use ($student) {
                $parent->user->notify(
                    new StudentNotification($student, StudentNotification::TYPE_UPDATED)
                );
            });

            return $student->fresh();
        });
    }

    public function delete(Student $student){
        DB::transaction(function() use($student){
            $student->deletePhoto();
            $student->delete();
        });
    }

    public function saveImage(Student $student, $data){
        if(!empty($data['photo'])){
            /** @var UploadedFile|null $photo */
            $photo = $data['photo'];
            if(!$photo->isValid()){
                return;
            }
            if($student->photo && Storage::disk('public')->exists($student->photo)){
                Storage::disk('public')->delete($student->photo);
            }
            $path = $photo->store('students', 'public');
            if($path){
                $student->photo = $path;
                $student->save();
            }
        }
    }
}