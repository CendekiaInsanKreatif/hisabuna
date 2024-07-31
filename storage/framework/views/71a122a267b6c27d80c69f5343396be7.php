<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'field' => [], 'show' => false, 'maxWidth' => '2xl', 'data' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['name', 'field' => [], 'show' => false, 'maxWidth' => '2xl', 'data' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];

?>

<div x-data="{
    show: <?php echo \Illuminate\Support\Js::from($show)->toHtml() ?>,
    title: '',
    data: {},
    route: '',
    method: '',
    name: '',
    type: '',
    isDetail: '',
    detailCount: 0,
    focusables() {
        let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
        return [...$el.querySelectorAll(selector)]
            .filter(el => !el.hasAttribute('disabled'));
    },
    firstFocusable() { return this.focusables()[0]; },
    lastFocusable() { return this.focusables().slice(-1)[0]; },
    nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable(); },
    prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable(); },
    nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1); },
    prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1; },
    formatNomorAkun(nomor_akun) {
        let formatted = nomor_akun.replace(/\D/g, '');
        if (formatted.length > 6) {
            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3, 5) + '-' + formatted.slice(5);
        } else if (formatted.length > 4) {
            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
        } else {
            formatted = formatted.slice(0, 3);
        }
        return formatted;
    },
    formatCurrency(event) {
            let input = event.target;
            let value = input.value.replace(/[^0-9]/g, ''); // Menghapus semua karakter non-numeric
            let parsedValue = parseFloat(value);
            if (isNaN(parsedValue)) {
                parsedValue = 0;
            }
            const formattedValue = parsedValue.toLocaleString('id-ID');
            input.value = formattedValue;
            // Menyimpan nilai mentah jika diperlukan
            this.data[input.name] = parsedValue;
        },
    formatCurrencyValue(value) {
            if (typeof value === 'number') {
                return value.toLocaleString('id-ID');
            }
            return '';
    }
}" x-init="$watch('show', value => {
    if (value) {
        document.body.classList.add('overflow-y-hidden');
        <?php echo e($attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : ''); ?>

    } else {
        document.body.classList.remove('overflow-y-hidden');
    }
})"
    x-on:open-modal.window="console.log($event.detail); method = $event.detail.method; route = $event.detail.route; data = $event.detail.data; title = $event.detail.title; show = true; name = $event.detail.name; type = $event.detail.type; isDetail = $event.detail.isDetail; detailCount = $event.detail.count;"
    x-on:close-modal.window="show = false" x-on:close.stop="show = false" x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="show"
    class="fixed flex items-center inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: <?php echo e($show ? 'block' : 'none'); ?>;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <div x-show="show"
        class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-y-auto shadow-xl transform transition-all sm:w-full <?php echo e($maxWidth); ?> sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <template x-if="type == 'select'">
            <div x-data="{ search: '' }" class="card bg-white shadow-lg rounded-xl border border-gray-200 p-4">
                <div class="card-body mt-1 bg-white shadow-md rounded-lg p-1"
                    style="max-height: 300px; position: relative;">
                    <div class="sticky top-0 bg-white z-10">
                        <input type="text" placeholder="Cari Akun . . ." x-model="search" id="searchBarAkun"
                            class="w-full px-4 py-2 mb-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    </div>
                    <div class="overflow-y-auto" style="max-height: 250px;">
                        <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                            <thead class="sticky top-0 text-xs text-gray-700 uppercase text-center bg-gray-200">
                                <tr>
                                    <?php
                                        foreach ($field as $item) {
                                            echo '<th class="py-3 px-6">' . $item['label'] . '</th>';
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-100 text-center">
                                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="border-b cursor-pointer"
                                        x-show="Object.values(<?php echo e(json_encode($item2)); ?>).join(' ').toLowerCase().includes(search.toLowerCase())"
                                        x-on:click="
                                            let obj = { isDetail: isDetail, data: <?php echo e(json_encode($item2)); ?> };
                                            document.getElementById('searchBarAkun').value = '';
                                            document.getElementsByName('nama_akun[' + isDetail + ']')[0].value = obj.data.nama_akun;
                                            let firstChar = obj.data.nomor_akun.charAt(0);
                                            let teksDebit, teksKredit, styleDebit, styleKredit;
                                            if (firstChar === '1') {
                                                teksDebit = 'Bertambah';
                                                teksKredit = 'Berkurang';
                                                styleDebit = 'color: green;';
                                                styleKredit = 'color: red;';
                                            } else if (firstChar === '2' || firstChar === '3' || firstChar === '4') {
                                                teksDebit = 'Berkurang';
                                                teksKredit = 'Bertambah';
                                                styleDebit = 'color: red;';
                                                styleKredit = 'color: green;';
                                            } else if (firstChar === '5' || firstChar === '6') {
                                                teksDebit = 'Bertambah';
                                                teksKredit = 'Berkurang';
                                                styleDebit = 'color: green;';
                                                styleKredit = 'color: red;';
                                            }

                                            document.getElementsByName('no_akun[' + isDetail + ']')[0].value = formatNomorAkun(obj.data.nomor_akun);
                                            document.getElementsByName('debit[' + isDetail + ']')[0].placeholder = teksDebit;
                                            document.getElementsByName('kredit[' + isDetail + ']')[0].placeholder = teksKredit;
                                            $dispatch('close-modal');
                                        ">
                                        <?php $__currentLoopData = $field; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="py-2 px-4">
                                                <span
                                                    x-text="<?php echo e($item['name'] == 'nomor_akun' ? 'formatNomorAkun(' . json_encode($item2[$item['name']]) . ')' : json_encode($item2[$item['name']])); ?>"></span>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </template>

        <template x-if="type == 'custom'">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Excel</h2>
                <form method="POST" x-bind:action="route" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="file" name="file" id="file-input-button"
                        style="margin-top: 0.25rem; display: block; width: 100%; border: 1px solid #10B981; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); focus:border-color: #10B981; focus:ring: 0.5rem; focus:ring-color: #A7F3D0; focus:ring-opacity: 0.5;">
                    <div class="mt-4 flex justify-end space-x-2">
                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['type' => 'submit','class' => 'bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded']); ?>
                            <span>Import</span>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')','class' => 'bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-2 px-4 rounded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')','class' => 'bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-2 px-4 rounded']); ?>
                            <?php echo e(__('Cancel')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                    </div>
                </form>
            </div>
        </template>
        <template x-if="type == 'form'">
            <form method="POST" x-bind:action="route" class="p-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" x-bind:value="method">
                <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100 text-center" x-text="title"></h2>
                <?php if(is_array($field) && count($field) > 0): ?>
                <?php
                    // print_r($field);
                ?>
                    <div class="mt-4 flex flex-wrap" x-data="data">
                        <div class="w-full md:w-1/2 p-2">
                            <?php $__currentLoopData = $field; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($index % 2 == 0): ?>
                                    <div class="mt-4">
                                        <label for="<?php echo e($item['name']); ?>"><?php echo e(__($item['label'])); ?></label>
                                        <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => ''.e($item['name']).'','name' => ''.e($item['name']).'','type' => ''.e($item['type']).'','class' => 'mt-1 block w-full','xBind:readonly' => 'name.includes(\'show\')','xOn:input' => 'name.includes(\'saldo-awal\') ? formatCurrency($event) : \'\'','xBind:disabled' => 'name.includes(\'show\') ? true : (name.includes(\'saldo-awal\') ? data.saldo_normal == \'credit\' : false)','placeholder' => $item['name'] === 'no_transaksi' ? 'Generate By System' : __($item['label']),'xBind:value' => 'name.includes(\'create\') ? \'\' : (name.includes(\'saldo-awal\') ? formatCurrencyValue(data.'.e($item['name']).') : data.'.e($item['name']).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($item['name']).'','name' => ''.e($item['name']).'','type' => ''.e($item['type']).'','class' => 'mt-1 block w-full','x-bind:readonly' => 'name.includes(\'show\')','x-on:input' => 'name.includes(\'saldo-awal\') ? formatCurrency($event) : \'\'','x-bind:disabled' => 'name.includes(\'show\') ? true : (name.includes(\'saldo-awal\') ? data.saldo_normal == \'credit\' : false)','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['name'] === 'no_transaksi' ? 'Generate By System' : __($item['label'])),'x-bind:value' => 'name.includes(\'create\') ? \'\' : (name.includes(\'saldo-awal\') ? formatCurrencyValue(data.'.e($item['name']).') : data.'.e($item['name']).')']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="w-full md:w-1/2 p-2">
                            <?php $__currentLoopData = $field; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($index % 2 != 0): ?>
                                    <div class="mt-4">
                                        <label for="<?php echo e($item['name']); ?>"><?php echo e(__($item['label'])); ?></label>
                                        <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => ''.e($item['name']).'','name' => ''.e($item['name']).'','type' => ''.e($item['type']).'','class' => 'mt-1 block w-full','xBind:readonly' => 'name.includes(\'show\')','xOn:input' => 'name.includes(\'saldo-awal\') ? formatCurrency($event) : \'\'','xBind:disabled' => 'name.includes(\'show\') ? true : (name.includes(\'saldo-awal\') ? data.saldo_normal == \'debit\' : false)','placeholder' => ''.e(__($item['label'])).'','xBind:value' => 'name.includes(\'create\') ? \'\' : (name.includes(\'saldo-awal\') ? formatCurrencyValue(data.'.e($item['name']).') : data.'.e($item['name']).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($item['name']).'','name' => ''.e($item['name']).'','type' => ''.e($item['type']).'','class' => 'mt-1 block w-full','x-bind:readonly' => 'name.includes(\'show\')','x-on:input' => 'name.includes(\'saldo-awal\') ? formatCurrency($event) : \'\'','x-bind:disabled' => 'name.includes(\'show\') ? true : (name.includes(\'saldo-awal\') ? data.saldo_normal == \'debit\' : false)','placeholder' => ''.e(__($item['label'])).'','x-bind:value' => 'name.includes(\'create\') ? \'\' : (name.includes(\'saldo-awal\') ? formatCurrencyValue(data.'.e($item['name']).') : data.'.e($item['name']).')']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <template x-if="name.includes('jurnal')">
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead class="bg-gray-50">
                                <?php
                                    $header = ['Nomor Akun', 'Nama Akun', 'Debit', 'Kredit', 'Lampiran'];
                                ?>
                                <?php $__currentLoopData = $header; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th scope="col"
                                        class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <?php echo e($item); ?>

                                    </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 overflow-y-auto">
                                <template x-for="(detail, index) in data.details" :key="index">
                                    <tr>
                                        <td class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            x-text="detail.coa_akun"></td>
                                        <td class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            x-text="detail.coa.nama_akun"></td>
                                        <td class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            x-text="new Intl.NumberFormat('id-ID').format(detail.debit)"></td>
                                        <td class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            x-text="new Intl.NumberFormat('id-ID').format(detail.credit)"></td>
                                        <td
                                            class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a :href="`<?php echo e(asset('storage')); ?>/${detail.lampiran}`" target="_blank"
                                                class="inline-flex items-center px-2 py-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold rounded">
                                                <span>Lihat</span>
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                <?php endif; ?>
                <div class="mt-4 flex justify-end space-x-2">
                    <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['type' => 'submit','xShow' => '!name.includes(\'show\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','x-show' => '!name.includes(\'show\')']); ?>
                        <span x-text="name.includes('destroy') ? 'Hapus' : 'Simpan'"></span>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                    
                    <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')','class' => 'bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-2 px-4 rounded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')','class' => 'bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-2 px-4 rounded']); ?>
                        <?php echo e(__('Cancel')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                </div>
            </form>
        </template>
        <template x-if="type == 'delete'">
            <form method="POST" x-bind:action="route" class="p-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" x-bind:value="method">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center" x-text="title + ' ?'">
                </h2>
                <div class="mt-4 flex justify-center space-x-2">
                    <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['type' => 'submit','class' => 'bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded']); ?>
                        <?php echo e(__('Yes')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')','class' => 'bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-3 px-6 rounded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')','class' => 'bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-3 px-6 rounded']); ?>
                        <?php echo e(__('No')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                </div>
            </form>
        </template>
    </div>
</div>
<?php /**PATH /var/www/hisabuna/resources/views/components/modal.blade.php ENDPATH**/ ?>