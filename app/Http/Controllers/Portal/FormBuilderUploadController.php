<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FormBuilderUploadController extends Controller
{
    public function imageForm(Request $request): View
    {
        $this->authorizeProfile((int) $request->query('profile_id', 0));

        return view('legacy.formbuilder.upload-image', [
            'profileId' => (int) $request->query('profile_id', 0),
            'randomId' => (string) $request->query('random_id', ''),
        ]);
    }

    public function docForm(Request $request): View
    {
        $this->authorizeProfile((int) $request->query('profile_id', 0));

        return view('legacy.formbuilder.upload-doc', [
            'profileId' => (int) $request->query('profile_id', 0),
            'randomId' => (string) $request->query('random_id', ''),
            'multiple' => false,
        ]);
    }

    public function docMultiForm(Request $request): View
    {
        $this->authorizeProfile((int) $request->query('profile_id', 0));

        return view('legacy.formbuilder.upload-doc', [
            'profileId' => (int) $request->query('profile_id', 0),
            'randomId' => (string) $request->query('random_id', ''),
            'multiple' => true,
        ]);
    }

    public function storeImage(Request $request): Response
    {
        $profile = $this->authorizeProfile((int) $request->input('profile_id', 0));
        $file = $request->file('userfile');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return $this->uploadResult(false, 'Please choose a valid image file.');
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            return $this->uploadResult(false, 'Allowed types: JPG, JPEG, PNG, GIF.');
        }

        $name = 'fb_img_'.$profile->id.'_'.Str::random(8).'.'.$ext;
        $path = $file->storeAs('form-builder/images', $name, 'public');
        $basename = basename($path);

        // Legacy public path compatibility.
        $publicLegacy = public_path('images/form_builder_uploaded_images/'.$basename);
        @mkdir(dirname($publicLegacy), 0775, true);
        @copy(Storage::disk('public')->path($path), $publicLegacy);

        session(['form_builder_image_temp' => $basename]);

        return $this->uploadResult(true, $basename, [
            'uploadedFlag' => 'uploaded_file',
            'textboxId' => $request->input('textbox_id', ''),
            'filename' => $basename,
        ]);
    }

    public function storeDoc(Request $request): Response
    {
        $profile = $this->authorizeProfile((int) $request->input('profile_id', 0));
        $multiple = (bool) $request->boolean('multiple');
        $files = $multiple ? $request->file('userfile', []) : [$request->file('userfile')];

        if (! is_array($files)) {
            $files = [$files];
        }

        $saved = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf'], true)) {
                continue;
            }

            $name = 'fb_doc_'.$profile->id.'_'.Str::random(8).'.'.$ext;
            $path = $file->storeAs('form-builder/docs', $name, 'public');
            $basename = basename($path);

            $publicLegacy = public_path('images/formbuilder_upload/'.$basename);
            @mkdir(dirname($publicLegacy), 0775, true);
            @copy(Storage::disk('public')->path($path), $publicLegacy);

            $saved[] = $basename;
        }

        if ($saved === []) {
            return $this->uploadResult(false, 'Please choose a valid document file.');
        }

        if ($multiple) {
            $existing = (array) session('form_builder_doc_multi_temp', []);
            $merged = array_values(array_unique(array_merge($existing, $saved)));
            session(['form_builder_doc_multi_temp' => $merged]);
            $filename = implode(',', $merged);
        } else {
            session(['form_builder_doc_temp' => $saved[0]]);
            $filename = $saved[0];
        }

        $randomId = (string) $request->input('random_id', '');

        return $this->uploadResult(true, $filename, [
            'uploadedFlag' => 'uploaded_doc'.$randomId,
            'textboxId' => $request->input('textbox_id', 'textbox'.$randomId),
            'filename' => $filename,
        ]);
    }

    /**
     * @param  array{uploadedFlag?: string, textboxId?: string, filename?: string}  $meta
     */
    protected function uploadResult(bool $ok, string $message, array $meta = []): Response
    {
        $flag = $meta['uploadedFlag'] ?? '';
        $textboxId = $meta['textboxId'] ?? '';
        $filename = $meta['filename'] ?? '';

        $html = view('legacy.formbuilder.upload-result', [
            'ok' => $ok,
            'message' => $message,
            'uploadedFlag' => $flag,
            'textboxId' => $textboxId,
            'filename' => $filename,
        ])->render();

        return response($html);
    }

    protected function authorizeProfile(int $profileId): Profile
    {
        abort_if($profileId <= 0, 404);
        $profile = Profile::query()->findOrFail($profileId);
        $userId = Auth::id();
        abort_if($userId === null, 403);

        $allowed = ClientUser::query()
            ->where('auth_user_id', $userId)
            ->where('client_id', $profile->client_id)
            ->active()
            ->exists();

        abort_unless($allowed, 403);

        return $profile;
    }
}
