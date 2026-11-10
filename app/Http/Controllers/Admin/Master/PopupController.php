<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::latest()->get();

        return view('admin.master.popup.index', compact('popups'));
    }

    public function create()
    {
        return view('admin.master.popup.create');
    }

    /**
     * Create a new popup.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePopup($request);

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $this->savePopupImage(
                    $request->file('image')
                );

                $validated['image'] = $imagePath;
            }

            $validated['status'] = $request->boolean('status');
            $validated['created_by'] = auth()->id();

            Popup::create($validated);
        } catch (Throwable $exception) {
            // Clean up the newly uploaded image if creation fails.
            if ($imagePath) {
                $this->deletePopupImage($imagePath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', __('Unable to create popup. Please try again.'));
        }

        return redirect()
            ->route('master.popup.index')
            ->with('success', __('Popup created successfully.'));
    }

    /**
     * Show the edit form.
     */
    public function edit($id)
    {
        $popup = Popup::findOrFail(decrypt($id));

        return view('admin.master.popup.edit', compact('popup'));
    }

    /**
     * Update an existing popup.
     */
    public function update(Request $request, $id)
    {
        $popup = Popup::findOrFail(decrypt($id));

        $validated = $this->validatePopup($request, true);

        $oldImage = $popup->image;
        $newImagePath = null;
        $newImageUploaded = $request->hasFile('image');

        try {
            if ($newImageUploaded) {
                // Save the replacement image first.
                $newImagePath = $this->savePopupImage(
                    $request->file('image')
                );

                $validated['image'] = $newImagePath;
            }

            $validated['status'] = $request->boolean('status');
            $validated['updated_by'] = auth()->id();

            $popup->update($validated);
        } catch (Throwable $exception) {
            // Preserve the existing image if the update fails.
            if ($newImagePath) {
                $this->deletePopupImage($newImagePath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', __('Unable to update popup. Please try again.'));
        }

        // Remove the previous image only after a successful update.
        if ($newImageUploaded && $oldImage && $oldImage !== $newImagePath) {
            $this->deletePopupImage($oldImage);
        }

        return redirect()
            ->route('master.popup.index')
            ->with('success', __('Popup updated successfully.'));
    }

    /**
     * Delete a popup and its image.
     */
    public function destroy($id)
    {
        $popup = Popup::findOrFail(decrypt($id));

        $image = $popup->image;

        try {
            $popup->delete();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('master.popup.index')
                ->with('error', __('Unable to delete popup. Please try again.'));
        }

        if ($image) {
            $this->deletePopupImage($image);
        }

        return redirect()
            ->route('master.popup.index')
            ->with('success', __('Popup deleted successfully.'));
    }

    /**
     * Save the uploaded image to public/storage/popups.
     * The database stores a relative path such as popups/example.png.
     */
    private function savePopupImage($file): string
    {
        $directory = public_path('storage/popups');

        File::ensureDirectoryExists($directory);

        $filename = $file->hashName();

        $file->move($directory, $filename);

        return 'popups/' . $filename;
    }

    /**
     * Delete an image only from the popup image directory.
     */
    private function deletePopupImage(string $image): void
    {
        $image = ltrim(str_replace('\\', '/', $image), '/');

        // Accept only filenames inside the popups directory.
        if (!preg_match('/\Apopups\/[A-Za-z0-9._-]+\z/', $image)) {
            return;
        }

        // Delete the image used by the website.
        $publicImage = public_path('storage/' . $image);

        if (File::isFile($publicImage)) {
            File::delete($publicImage);
        }

        // Remove any matching legacy copy from Laravel's public disk.
        if (Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }
    }

    /**
     * Validate popup form data.
     */
    private function validatePopup(
        Request $request,
        bool $isUpdate = false
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],

            'message' => [
                'nullable',
                'string',
            ],

            'note' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'button_text' => [
                'nullable',
                'string',
                'max:100',
            ],

            'button_url' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                Rule::when(
                    $request->filled('starts_at'),
                    ['after_or_equal:starts_at']
                ),
            ],

            'status' => [
                'nullable',
                'boolean',
            ],
        ]);
    }
}
