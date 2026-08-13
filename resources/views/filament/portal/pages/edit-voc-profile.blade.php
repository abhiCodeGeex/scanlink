<x-filament-panels::page>
    <div class="scanlink-container sl-profile-editor sl-profile-editor--voc clearfix">
        <section class="add-form-left sl-add-form-left" style="width:100%;max-width:720px;margin:0 auto;float:none;">
            <form wire:submit="save">
                {{ $this->form }}

                <ul class="form-view clearfix" style="margin-top:16px;">
                    <li class="no-float" style="text-align:center;width:100%;float:none;">
                        <button
                            type="submit"
                            class="green-btn"
                            wire:loading.attr="disabled"
                        >Save changes</button>
                    </li>
                </ul>
            </form>
        </section>
    </div>
</x-filament-panels::page>
