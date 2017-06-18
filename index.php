<? require_once "header.php"; ?>
<? require_once "only_once/add_txt_to_db.php"; 


?>
<div class="main-menu">
    <ul>
		<li class="text-menu" data-timeframe="m1">
            <span>M1</span>
        </li>
		<li class="text-menu" data-timeframe="m5">
            <span>M5</span>
        </li>
		<li class="text-menu" data-timeframe="m15">
            <span>M15</span>
        </li>
		<li class="text-menu" data-timeframe="m30">
            <span>M30</span>
        </li>
		<li class="text-menu" data-timeframe="h1">
            <span>H1</span>
        </li>
		<li class="text-menu" data-timeframe="h4">
            <span>H4</span>
        </li>
		<li class="text-menu" data-timeframe="d1">
            <span>D1</span>
        </li>
		<li class="text-menu" data-timeframe="w1">
            <span>W1</span>
        </li>
		<li class="text-menu" data-timeframe="mn">
            <span>MN</span>
        </li>
		<li class="menu-separator"></li>
        <li class="draw-figure-for-seeking" style="display:none">
            <div>
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </div>
        </li>
		
        <li class="mode-editor text-menu">
            <span>Режим редактора</span>
        </li>
		<li class="send-seek-button text-menu" style="display:none">
            <span>Найти совпадения</span>
        </li>
    </ul>
</div>
<div class="windows">
	<div class="indicator-OHLC"></div>
</div>
<div class="block_settings" style="display: none">
	<div class="block_settings__group block_name">
		<div class="group_title">Имя</div>
		<div class="group_line">
			<label> 
				<input id="name" type="text" placeholder="" min="0" step="1" >
			</label>
		</div>
	</div>
	<div class="block_settings__group bar_count">
		<div class="group_title">Кол-во баров</div>
		<div class="group_line">
			<label>min: 
				<input id="min-count-bars" type="number" placeholder="min" min="0" step="1" >
			</label>
			<label>max: 
				<input id="max-count-bars" type="number" placeholder="max" min="0" step="1" >
			</label>
		</div>
	</div>
	<div class="block_settings__group bar_count">
		<div class="group_title">Кол-во пунктов</div>
		<div class="group_line">
			<label>min: 
				<input id="min-count-pt" type="number" placeholder="min" min="0" >
			</label>
			<label>max: 
				<input id="max-count-pt" type="number" placeholder="max" min="0" >
			</label>
		</div>
	</div>
	<div class="block_settings__group bar_count">
		<div class="group_title">Цена</div>
		<div class="group_line">
			<label>min: 
				<input id="min-price" type="number" placeholder="min" min="0" >
			</label>
			<label>max: 
				<input id="max-price" type="number" placeholder="max" min="0" >
			</label>
		</div>
		<div style="padding-top: 20px">
			<label title="Если чекбокс не выбран, то цена при поиске должна попасть в заданный диапазон, если выбран - то цена должна попасть в диапазон с соблюдением правил по границам">
				<input id="strong-price-diapason" type="checkbox"> Ограничение строгое
			</label>
		</div>
		<div class="price-diapason" style="margin-top: 10px">
			<div class="group-line-title">Погрешность цены (в пунктах)</div>
			<div class="group_line content-after">
				<label title="Погрешность нижней границы цены">min: 
					<input id="min-error-price" type="number" placeholder="min" min="0" >
				</label>
				<label title="Погрешность верхней границы цены">max: 
					<input id="max-error-price" type="number" placeholder="max" min="0" >
				</label>
			</div>
			<label title="Погрешность будет вычисляться в %">
				<input id="is-unit-measure-in-percent" type="checkbox"> В процентах
			</label>
		</div>
	</div>
	
	<div class="block_settings__group bar_count">
		<div class="group_title">Дата</div>
		<div class="wrapper_group_line">
			<div class="wrapper_group-line-title">Год:</div>
			<div class="group_line">
				<label>
					<input id="min-year" type="number" placeholder="от" min="0" >
				</label>
				<label>
					<input id="max-year" type="number" placeholder="до" min="0" >
				</label>
			</div>
		</div>
		<div class="wrapper_group_line">
			<div class="wrapper_group-line-title">Месяц:</div>
			<div class="group_line">
				<label>
					<input id="min-month" type="number" placeholder="от" min="0" >
				</label>
				<label>
					<input id="max-month" type="number" placeholder="до" min="0" >
				</label>
			</div>
		</div>
		<div class="wrapper_group_line">
			<div class="wrapper_group-line-title">День:</div>
			<div class="group_line">
				<label>
					<input id="min-day" type="number" placeholder="от" min="0" >
				</label>
				<label>
					<input id="max-day" type="number" placeholder="до" min="0" >
				</label>
			</div>
		</div>
		<div class="wrapper_group_line">
			<div class="wrapper_group-line-title">Час:</div>
			<div class="group_line">
				<label>
					<input id="min-hour" type="number" placeholder="от" min="0" >
				</label>
				<label>
					<input id="max-hour" type="number" placeholder="до" min="0" >
				</label>
			</div>
		</div>
		<div class="wrapper_group_line">
			<div class="wrapper_group-line-title">Минута:</div>
			<div class="group_line">
				<label>
					<input id="min-minute" type="number" placeholder="от" min="0" >
				</label>
				<label>
					<input id="max-minute" type="number" placeholder="до" min="0" >
				</label>
			</div>
		</div>
	</div>
	

	
	


</div>
<? require_once "footer.php"; ?>
