<?php
/*
	Section: Column
	Class Name: PLColumn
	Filter: layout
	Loading: active
*/

class PLColumn extends PageLinesSection {


	function section_template() {

		?>
		<div class="pl-sortable-column pl-sortable-area editor-row">

			<?php

			echo render_nested_sections( $this->meta['content'], 2 );

			?>
			<span class="pl-column-forcer">&nbsp;</span>
		</div>
	<?php

	}

}