<?php
/* @var $this yii\web\View */
/** @var $domains array */
?>
<table>
	<thead>
	<tr>
		<th>Url</th>
		<th>Count urls</th>
		<th>Depth level</th>
		<th>Execution time</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($domains as $domain): ?>
		<?php foreach ($domain->getExternalUrls() as $url): ?>
			<tr>
				<td><?= $url['url'] ?></td>
				<td><?= $url['count'] ?></td>
				<td><?= $url['depth'] ?></td>
				<td><?= $url['executionTime'] ?></td>
			</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</tbody>
</table>