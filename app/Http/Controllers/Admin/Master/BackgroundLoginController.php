<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\BackgroundLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BackgroundLoginController extends Controller
{
    private const IMAGE_DIRECTORY = 'uploads/background_login';

    public function index()
    {
        $backgroundLogin = BackgroundLogin::first();

        return view(
            'admin.master.background_login.index',
            compact('backgroundLogin')
        );
    }

    public function create()
    {
        $backgroundLogin = BackgroundLogin::first();

        if ($backgroundLogin) {
            return redirect()->route(
                'frontend-login.background-login.edit',
                encrypt($backgroundLogin->id)
            );
        }

        return view('admin.master.background_login.create');
    }

    public function store(Request $request)
    {
        if (BackgroundLogin::exists()) {
            $backgroundLogin = BackgroundLogin::first();

            return redirect()->route(
                'frontend-login.background-login.edit',
                encrypt($backgroundLogin->id)
            );
        }

        $request->validate([
            'background_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'login_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'status' => 'required|in:0,1',
        ]);

        $backgroundImage = $request->file('background_image')
            ->store(self::IMAGE_DIRECTORY, 'public');

        $loginImage = $request->file('login_image')
            ->store(self::IMAGE_DIRECTORY, 'public');

        // Make uploaded images available through public/storage as well.
        $this->syncPublicImage($backgroundImage);
        $this->syncPublicImage($loginImage);

        BackgroundLogin::create([
            'background_image' => $backgroundImage,
            'login_image' => $loginImage,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('frontend-login.background-login.index')
            ->with('success', 'Background Login created successfully');
    }

    public function edit($id)
    {
        $backgroundLogin = BackgroundLogin::findOrFail(decrypt($id));

        return view(
            'admin.master.background_login.edit',
            compact('backgroundLogin')
        );
    }

    public function update(Request $request, $id)
    {
        $backgroundLogin = BackgroundLogin::findOrFail(decrypt($id));

        $request->validate([
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'login_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('background_image')) {
            $newBackgroundImage = $request->file('background_image')
                ->store(self::IMAGE_DIRECTORY, 'public');

            // Copy the new image before removing the old one.
            $this->syncPublicImage($newBackgroundImage);

            $this->deleteImage($backgroundLogin->background_image);

            $data['background_image'] = $newBackgroundImage;
        }

        if ($request->hasFile('login_image')) {
            $newLoginImage = $request->file('login_image')
                ->store(self::IMAGE_DIRECTORY, 'public');

            // Copy the new image before removing the old one.
            $this->syncPublicImage($newLoginImage);

            $this->deleteImage($backgroundLogin->login_image);

            $data['login_image'] = $newLoginImage;
        }

        $backgroundLogin->update($data);

        return redirect()
            ->route('frontend-login.background-login.index')
            ->with('success', 'Background Login updated successfully');
    }

    public function destroy($id)
    {
        $backgroundLogin = BackgroundLogin::findOrFail(decrypt($id));

        $this->deleteImage($backgroundLogin->background_image);
        $this->deleteImage($backgroundLogin->login_image);

        $backgroundLogin->delete();

        return redirect()
            ->route('frontend-login.background-login.index')
            ->with('success', 'Background Login deleted successfully');
    }

    /**
     * Copy an uploaded image into the existing public/storage folder.
     * If public/storage already points to storage/app/public, no copy is needed.
     */
    private function syncPublicImage(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        $source = Storage::disk('public')->path($relativePath);
        $publicStorageRoot = public_path('storage');
        $destination = $publicStorageRoot . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (!File::exists($source)) {
            throw new RuntimeException(
                'Uploaded image was not found: ' . $relativePath
            );
        }

        // Avoid copying a file onto itself when storage is a real symlink.
        $realPublicStorage = realpath($publicStorageRoot);
        $realStorageDisk = realpath(storage_path('app/public'));

        if (
            $realPublicStorage !== false &&
            $realStorageDisk !== false &&
            $realPublicStorage === $realStorageDisk
        ) {
            return;
        }

        File::ensureDirectoryExists(dirname($destination));

        if (!File::copy($source, $destination)) {
            throw new RuntimeException(
                'Could not copy image to public/storage: ' . $relativePath
            );
        }
    }

    /**
     * Delete only the specified image from both locations.
     */
    private function deleteImage(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $publicStorageRoot = public_path('storage');
        $realPublicStorage = realpath($publicStorageRoot);
        $realStorageDisk = realpath(storage_path('app/public'));

        // If both paths resolve to the same storage, it was already deleted.
        if (
            $realPublicStorage !== false &&
            $realStorageDisk !== false &&
            $realPublicStorage === $realStorageDisk
        ) {
            return;
        }

        $publicImage = $publicStorageRoot . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (File::isFile($publicImage)) {
            File::delete($publicImage);
        }
    }
}
