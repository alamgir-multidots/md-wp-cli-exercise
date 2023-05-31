import FeedbacksList from '@Admin/components/tabs/FeedbacksList';

function classNames( ...classes ) {
	return classes.filter( Boolean ).join( ' ' );
}

function Feedbacks( props ) {
	const { navigation, tab, navigate } = props;
	return (
		<main className="pl-5 pt-5">
			<div className="max-w-[98%] bg-white shadow rounded">
				<div className="mb-0 sm:px-6 lg:px-0 lg:col-span-9">
					{ 'md_wp_cli_exercise_setting' === tab && (
						<>
							<FeedbacksList />
						</>
					) }
				</div>
			</div>
		</main>
	);
}

export default Feedbacks;