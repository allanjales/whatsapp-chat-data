<?php

date_default_timezone_set('America/Sao_Paulo');

//Count time
$time[0] = microtime(true);

//File to read
$fileName = 'conversa.txt';

//All messages
$message = [];

//If you can read the file
if (is_readable($fileName))
{
	//get content of file
	$content = file_get_contents($fileName);
		
	//Gambiarra para o pregmatch à vir funcionar em todas as mensagens
	$content .= '00/00/0000 00:00 - 0: ';
	
	preg_match_all('/(\d{2}\/\d{2}\/\d{4}) (\d{2}:\d{2}) - (?!As mensagens e chamadas desta)(.+?): (.*?)(?=\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} - .+?: )/s', $content, $matches);

	//Fill variable $message with each message read
	for ($i = 0; $i < count($matches[0]); $i++)
	{
		//Add to array
		$message[] = [
			'from' => $matches[3][$i],
			'text' => $matches[4][$i],
			'date' => $matches[1][$i],
			'time' => $matches[2][$i]
			];
	}
}


?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Whatsapp chat data</title>
<meta name="author" content="Allan Jales">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<style>

.chart{
	/*background-color: aliceblue;*/
    width: 720px;
    margin: auto;
    padding-bottom: 10px;
}

.chart-title{
	user-select: none;
}

</style>
</head>

<body class="bg-light">
<nav class="navbar navbar-expand-md bg-success navbar-dark sticky-top">
	<div class="container">
		<a class="navbar-brand" href="/wppdata.php">Whatsapp chat data by Allan Jales</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end" id="menu">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="https://github.com/allanjales/whatsapp-chat-data">Github</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
<main class="container bg-white">
	<div class="mx-auto pb-4">
		<?php if (!empty($message)): ?> 
			<p id="construction">Carregando</p>
			<h1 id="chartMonthTitle" class="mt-3 chart-title">Mensagens por mês</h1>
			<div class="d-flex flex-column chart">
				<canvas id="msgChartMonth"></canvas>
			</div>
			<h1 id="chartDayTitle" class="mt-3 chart-title">Mensagens por dia</h1>
			<div class="d-flex flex-column chart">
				<canvas id="msgChartDay"></canvas>
			</div>
			<h1 id="chartHourTitle" class="mt-3 chart-title">Mensagens por hora</h1>
			<div class="d-flex flex-column chart">
				<canvas id="msgChartHour"></canvas>
			</div>
			<p class="mt-4 mb-1"><?php echo count($message); ?> mensagens</p>
			<p>Última mensagem do dia <?php echo end($message)['date'].' às '.end($message)['time']; ?></p>
		<?php else: ?>
			<h1 class="pt-3">Ops!</h1>
			<h6>Não foi possível achar o arquivo</h6>
			<p>Verifique se você tem um arquivo no diretório com o nome "<?php echo $fileName; ?>"</p>
		<?php endif; ?>
		<div class="mt-5">
			<h5>Como funciona</h5>
			<ul>
				<li>Clique numa barra para <b>selecionar</b>;</li>
				<li>Selecione uma barra no histograma <a href="#chartMonthTitle" title="Ir para o gráfico">Mensagens por mês</a> para dar zoom no mês em <a href="#chartDayTitle" title="Ir para o gráfico">Mensagens por dia</a>;</li>
				<li>Selecione uma barra no histograma <a href="#chartDayTitle" title="Ir para o gráfico">Mensagens por dia</a> para ter uma visualização em horas do dia clicado em <a href="#chartHourTitle" title="Ir para o gráfico">Mensagens por hora</a>;</li>
				<li>Você pode clicar numa barra selecionada para desselecionar;</li>
				<li>Barras selecionadas são destacadas com cores diferentes das demais;</li>
				<li>Em <a href="#chartHourTitle" title="Ir para o gráfico">Mensagens por hora</a> as cores vermelhas aparecem quando estiver visualizando conversas de um dia específico.</li>
			</ul>
		</div>
	</div>
</main>

</body>
</html>
<?php if (!empty($message)): ?>
	<?php
	$msgCount = [];
	
	/*
	Object format reference
	msgCount =
	{
		'month':
		{
			"m/Y": quantity
		},
		'day':
		{
			"d/m/Y": quantity
		},
		'hour':
		{
			"H": quantity
		}
		"m/Y":
		{
			'day':
			{
				"d/m/Y": quantity
			},
			'hour':
			{
				"H": quantity
			},
			"d/m/Y":
			{
				"H": quantity
			}
		}
	}
	*/

	//Create all keys of array
	{	
		//Get first message date time parameters on international mode (Y-m-d H:i)
		$firstDateTime = substr($message[0]['date'], 0, 2).'-'.substr($message[0]['date'], 3, 2).'-'.substr($message[0]['date'], 6).' '.substr($message[0]['time'], 0, 2).':'.substr($message[0]['time'], 3);
		$lastDateTime = substr(end($message)['date'], 0, 2).'-'.substr(end($message)['date'], 3, 2).'-'.substr(end($message)['date'], 6).' '.substr(end($message)['time'], 0, 2).':'.substr($message[0]['time'], 3);

		//Get timestamp of first date time and then the last timestamp
		$timestamp = strtotime($firstDateTime);
		$lastTimestamp = strtotime($lastDateTime);
		//$lastTimestamp = time();

		//Loop until last timestamp summing hour
		while ($timestamp <= $lastTimestamp)
		{
			//Creates variable
			$msgCount['month'][date('m/Y', $timestamp)] = 0;
			$msgCount['day'][date('d/m/Y', $timestamp)] = 0;
			$msgCount['hour'][intval(date('H', $timestamp))] = 0;
			$msgCount[date('m/Y', $timestamp)]['day'][date('d/m/Y', $timestamp)] = 0;
			$msgCount[date('m/Y', $timestamp)]['hour'][intval(date('H', $timestamp))] = 0;
			$msgCount[date('m/Y', $timestamp)][date('d/m/Y', $timestamp)][intval(date('H', $timestamp))] = 0;

			//Add one minute to timestamp
			$timestamp = strtotime('+1 hour', $timestamp);
		}
	}

	//Fill array with values
	{
		//Read each message date time
		foreach ($message as $msg)
		{
			//Get message date time parameters
			$dateTimeParameter = [
				substr($msg['date'], 6),		// Y
				substr($msg['date'], 3),		// m/Y
				$msg['date'],					// d/m/Y
				substr($msg['time'], 0, 2),		// H
				];

			//Add +1 to each corresponding variable
			$msgCount['month'][$dateTimeParameter[1]] += 1;
			$msgCount['day'][$dateTimeParameter[2]] += 1;
			$msgCount['hour'][intval($dateTimeParameter[3])] += 1;
			$msgCount[$dateTimeParameter[1]]['day'][$dateTimeParameter[2]] += 1;
			$msgCount[$dateTimeParameter[1]]['hour'][intval($dateTimeParameter[3])] += 1;
			$msgCount[$dateTimeParameter[1]][$dateTimeParameter[2]][intval($dateTimeParameter[3])] += 1;
		}
	}
	?>

	<script>
	//Parse $msgCount from PHP to JS
	var msgCount = JSON.parse(<?php echo "'".json_encode($msgCount)."'"; ?>);

	//Creates month dhart
	var ctx = document.getElementById('msgChartMonth').getContext('2d');
	var msgChartMonth = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: Object.keys(msgCount['month']),
			datasets: [{
				label: 'Messagens',
				data: Object.values(msgCount['month']),
				backgroundColor: [
					<?php for ($i = 0; $i < count($msgCount['month']); $i++): ?>
						'rgba(54, 162, 235, 0.2)',
					<?php endfor; ?>
				],
				borderColor: [
					<?php for ($i = 0; $i < count($msgCount['month']); $i++): ?>
						'rgba(54, 162, 235, 1)',
					<?php endfor; ?>
				],
				borderWidth: 1
			}]
			},
		options:
		{
			tooltips:
			{
				mode: 'x',
				intersect: false
			},
			hover:
			{
				onHover: function(e)
				{
					//Get the element that is point to
					var point = this.getElementAtEvent(e);

					//IF is point to an element, show pointer cursor
					if (point.length)
						e.target.style.cursor = 'pointer';
					else
						e.target.style.cursor = 'default';
				}
			},
			scales:
			{
				yAxes: [{
					ticks:
					{
						beginAtZero: true,
						precision: 0
					},
					scaleLabel:
					{
						display: true,
						labelString: 'Mensagens'
					}
				}],
				xAxes: [{
					scaleLabel:
					{
						display: false,
						labelString: 'Mês',
						padding:
						{
							top: 0,
							bottom: 0
						}
					}
				}]
			},
			legend:
			{
				display: false
			},
			title:
			{
				display: true,
				text: 'Mensagens por mês na vida'
			},
			onClick: function(e){
				//Default parameters
				var defaultBackgroundColor = 'rgba(54, 162, 235, 0.2)';
				var defaultBorderColor = 'rgba(54, 162, 235, 0.2)';

				//Get the element
				var element = this.getElementAtEvent(e);

				//If selected a element
				if(element.length > 0)
				{
					//If it is not selected
					if (element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] == defaultBackgroundColor)
					{
						//Restore graph color
						for(var i=0; i<element[0]._chart.config.data.datasets[0].backgroundColor.length; i++){
							element[0]._chart.config.data.datasets[0].backgroundColor[i] = defaultBackgroundColor;
							element[0]._chart.config.data.datasets[0].borderColor[i] = defaultBorderColor;
						}

						//Change color of current bar
						element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] = 'rgba(255, 206, 86, 0.2)';
						element[0]._chart.config.data.datasets[0].borderColor[element[0]._index] = 'rgba(255, 206, 86, 1)';
					}
					else
					{
						//Restore graph color
						element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] = defaultBackgroundColor;
						element[0]._chart.config.data.datasets[0].borderColor[element[0]._index] = defaultBorderColor;
					}

					//Update this Graph
					this.update();

					//Get current label
					var label = element[0]._chart.config.data.labels[element[0]._index];

					//If it is not selected
					if (element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] == defaultBackgroundColor)
					{
						//Restore the day graph
						msgChartDay.config.options.scales.xAxes[0].ticks.min = msgChartDay.data.datasets[0].data[0];
						msgChartDay.config.options.scales.xAxes[0].ticks.max = msgChartDay.data.datasets[0].data[msgChartDay.data.datasets[0].data.length-1];
						msgChartDay.options.title.text = 'Mensagens por dia na vida';
						msgChartDay.update();
					}
					else
					{
						//Get first and last day of the clicked month
						var firstDayMonth = '01/'+label;
						var lastDayMonth = (new Date(label.substr(3), label.substr(0,2), 0).getDate())+'/'+label;

						//Changes the day graph
						msgChartDay.config.options.scales.xAxes[0].ticks.min = firstDayMonth;
						msgChartDay.config.options.scales.xAxes[0].ticks.max = lastDayMonth;
						msgChartDay.options.title.text = 'Mensagens por dia em '+label;
						msgChartDay.update();
					}
				}
			}
		}
	});

	//Creates day chart
	var ctx = document.getElementById('msgChartDay').getContext('2d');
	var msgChartDay = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: Object.keys(msgCount['day']),
			datasets: [{
				label: 'Messagens',
				data: Object.values(msgCount['day']),
				backgroundColor: [
					<?php for ($i = 0; $i < count($msgCount['day']); $i++): ?>
						'rgba(255, 206, 86, 0.2)',
					<?php endfor; ?>
				],
				borderColor: [
					<?php for ($i = 0; $i < count($msgCount['day']); $i++): ?>
						'rgba(255, 206, 86, 1)',
					<?php endfor; ?>
				],
				borderWidth: 1
			}]
			},
		options:
		{
			tooltips:
			{
				mode: 'x',
				intersect: false
			},
			hover:
			{
				onHover: function(e)
				{
					//Get the element that is point to
					var point = this.getElementAtEvent(e);

					//IF is point to an element, show pointer cursor
					if (point.length)
						e.target.style.cursor = 'pointer';
					else
						e.target.style.cursor = 'default';
				}
			},
			scales:
			{
				yAxes: [{
					ticks:
					{
						beginAtZero: true,
						precision: 0
					},
					scaleLabel:
					{
						display: true,
						labelString: 'Mensagens'
					}
				}],
				xAxes: [{
					scaleLabel:
					{
						display: false,
						labelString: 'Dia',
						padding:
						{
							top: 0,
							bottom: 0
						}
					}
				}]
			},
			legend:
			{
				display: false
			},
			title:
			{
				display: true,
				text: 'Mensagens por dia na vida'
			},
			onClick: function(e){
				//Default parameters
				var defaultBackgroundColor = 'rgba(255, 206, 86, 0.2)';
				var defaultBorderColor = 'rgba(255, 206, 86, 1)';

				//Get the element
				var element = this.getElementAtEvent(e);

				//If selected a element
				if(element.length > 0)
				{
					//If it is not selected
					if (element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] == defaultBackgroundColor)
					{
						//Restore graph color
						for(var i=0; i<element[0]._chart.config.data.datasets[0].backgroundColor.length; i++){
							element[0]._chart.config.data.datasets[0].backgroundColor[i] = defaultBackgroundColor;
							element[0]._chart.config.data.datasets[0].borderColor[i] = defaultBorderColor;
						}

						//Change color of current bar
						element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] = 'rgba(255, 99, 132, 0.2)';
						element[0]._chart.config.data.datasets[0].borderColor[element[0]._index] = 'rgba(255, 99, 132, 1)';
					}
					else
					{
						//Restore graph color
						element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] = defaultBackgroundColor;
						element[0]._chart.config.data.datasets[0].borderColor[element[0]._index] = defaultBorderColor;
					}

					//Update this Graph
					this.update();

					//Get current label
					var label = element[0]._chart.config.data.labels[element[0]._index];

					//If it is not selected
					if (element[0]._chart.config.data.datasets[0].backgroundColor[element[0]._index] == defaultBackgroundColor)
					{
						//Restore the day graph
						msgChartHour.data.datasets[0].data = msgCount['hour'];
						msgChartHour.data.datasets[0].backgroundColor.fill('rgba(75, 192, 192, 0.2)');
						msgChartHour.data.datasets[0].borderColor.fill('rgba(75, 192, 192, 1)');
						msgChartHour.options.title.text = 'Mensagens por hora na vida';
						msgChartHour.update();
					}
					else
					{
						//Changes the day graph
						msgChartHour.data.datasets[0].data = msgCount[label.substr(3)][label];
						msgChartHour.data.datasets[0].backgroundColor.fill('rgba(255, 99, 132, 0.2)');
						msgChartHour.data.datasets[0].borderColor.fill('rgba(255, 99, 132, 1)');
						msgChartHour.options.title.text = 'Mensagens por hora '+label;
						msgChartHour.update();
					}
				}
			}
		}
	});

	//Creates hour chart
	var ctx = document.getElementById('msgChartHour').getContext('2d');
	var msgChartHour = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['00h','01h','02h','03h','04h','05h','06h','07h','08h','09h','10h','11h','12h','13h','14h','15h','16h','17h','18h','19h','20h','21h','22h','23h'],
			datasets: [{
				label: 'Messagens',
				data: Object.values(msgCount['hour']),
				backgroundColor: [
					<?php for ($i = 0; $i < count($msgCount['hour']); $i++): ?>
						'rgba(75, 192, 192, 0.2)',
					<?php endfor; ?>
				],
				borderColor: [
					<?php for ($i = 0; $i < count($msgCount['hour']); $i++): ?>
						'rgba(75, 192, 192, 1)',
					<?php endfor; ?>
				],
				borderWidth: 1
			}]
			},
		options:
		{
			tooltips:
			{
				mode: 'x',
				intersect: false
			},
			scales:
			{
				yAxes: [{
					ticks:
					{
						beginAtZero: true,
						precision: 0
					},
					scaleLabel:
					{
						display: true,
						labelString: 'Mensagens'
					}
				}],
				xAxes: [{
					scaleLabel:
					{
						display: false,
						labelString: 'Hora',
						padding:
						{
							top: 0,
							bottom: 0
						}
					}
				}]
			},
			legend:
			{
				display: false
			},
			title:
			{
				display: true,
				text: 'Mensagens por hora na vida'
			}
		}
	});
	</script>
<?php endif; ?>

<script>
<?php

//Count time
$time[2] = microtime(true);
?>
//Show processing time
$("#construction").html("<?php echo 'Construção em '.number_format($time[2] - $time[0], 3).' ms';?>");
</script>

<script>
	$('a[href^="#"]').on('click', function(event) {

		var target = $(this.getAttribute('href'));

		if( target.length ) {
			event.preventDefault();
			$('html, body').stop().animate({
				scrollTop: (target.offset().top)-56 //SUBTRAIR AQUI A ALTURA DO MENU
			}, 400);
		}

	});
</script>