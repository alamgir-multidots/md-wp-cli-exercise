export const initialState = md_wp_cli_exercise_settings;

const reducer = ( state, action ) => {
	switch ( action.type ) {
		case 'CHANGE':
			return {
				...action.data,
			};

		default:
			return state;
	}
};

export default reducer;
