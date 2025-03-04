@props(['label','placeholder'])
<div class="mx-auto">
  <label  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label ?? "" }}</label>
  <select  placeholder="{{ $placeholder ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:primary-500 focus:primary-500 block w-full p-2.5 ">
    {{ $slot }}
  </select>
</div>

