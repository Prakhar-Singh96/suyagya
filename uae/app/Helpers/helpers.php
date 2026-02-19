<?php

use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;

if (!function_exists('uploadImage')) {
    /**
     * Upload Image Helper Function
     *
     * @param mixed $request
     * @param string $inputName
     * @param string $path
     * @return string|null
     */
    function uploadImage($request, $inputName, $path)
    {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            return $path . '/' . $filename;
        }
        return null;
    }
}

if (!function_exists('deleteImage')) {
    /**
     * Delete Image Helper Function
     *
     * @param string|null $path
     * @return void
     */
    function deleteImage($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
