<?php
namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherService {
    public function store(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'password' => Hash::make($data['password'] ?? Str::password()),
            ]);

            $user->assignRole('Teacher');

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'employee_number' => $data['employee_number'],
                'nationality'=> $data['nationality'] ?? null,
                'qualification' => $data['qualification'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'status' => $data['status'],
                'address' => $data['address'],
                'specialty' => $data['specialty'] ?? null,
                'bio'  => $data['bio'] ?? null,
            ]);

            $this->saveImageTeacher($teacher, $data);

            return $teacher;
        });
    }

    public function update(Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data) {

            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $teacher->user->update($userData);

            $teacher->update([
                'employee_number' => $data['employee_number'],
                'nationality'=> $data['nationality'] ?? null,
                'qualification' => $data['qualification'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'status' => $data['status'],
                'address' => $data['address'],
                'specialty' => $data['specialty'] ?? null,
                'bio'  => $data['bio'] ?? null,
            ]);

            $this->saveImageTeacher($teacher, $data);

            return $teacher->fresh();
        });
    }

    /**
     * Methode pour enregistrer la photo d'un enseignant
     * @param Teacher $teacher
     * @param mixed $data
     * @return void
     */
    public function saveImageTeacher(Teacher $teacher, $data){
        if(!empty($data['photo'])){
            /** @var UploadedFile|null $photo */
            $photo = $data['photo'];
            if(!$photo->isValid()){
                return;
            }
            if($teacher->photo && Storage::disk('public')->exists($teacher->photo)){
                Storage::disk('public')->delete($teacher->photo);
            }
            $path = $photo->store('teachers', 'public');
            if($path){
                $teacher->photo = $path;
                $teacher->save();
            }
        }
    }
}