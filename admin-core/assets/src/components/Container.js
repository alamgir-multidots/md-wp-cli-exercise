import React, { useState, useEffect, useRef } from 'react';
import { useLocation, useHistory } from 'react-router-dom';
import { __ } from '@wordpress/i18n';

import Notification from '@Admin/components/tabs/Notification';
import Header from '@Admin/components/Header';
import Settings from '@Admin/components/path/Settings';
import Feedbacks from '@Admin/components/path/Feedbacks';
import apiFetch from '@wordpress/api-fetch';
import { useStateValue } from '@Admin/components/Data';

function Container() {
	const [ data ] = useStateValue();
	const [ settingsTab, setSettingsTab ] = useState( '' );
	const query = new URLSearchParams( useLocation().search );
	const activePage = 'md_wp_cli_exercise_setting';
	const [ activePath, setActivePath ] =  useState( 'settings' );
	
	const tab = [
		'md_wp_cli_exercise_setting',
		'md_wp_cli_exercise_styling',
		'how',
	].includes( query.get( 'tab' ) )
		? query.get( 'tab' )
		: getSettingsTab();
	const [ processing, setProcessing ] = useState( false );

	const [ status, setStatus ] = useState( false );

	const updateData = useRef( false );

	useEffect( () => {
		setActivePath( query.get( 'path' ) );
	}, [query] );

	useEffect( () => {
		if ( ! updateData.current ) {
			updateData.current = true;
			return;
		}

		const formData = new window.FormData();

		formData.append( 'action', 'md_wp_cli_exercise_update_settings' );
		formData.append(
			'security',
			md_wp_cli_exercise_settings.update_nonce
		);
		formData.append(
			'md_wp_cli_exercise_general',
			JSON.stringify( data.md_wp_cli_exercise_general )
		);
		formData.append(
			'md_wp_cli_exercise_appearance',
			JSON.stringify( data.md_wp_cli_exercise_appearance )
		);

		setProcessing( true );

		apiFetch( {
			url: md_wp_cli_exercise_settings.ajax_url,
			method: 'POST',
			body: formData,
		} ).then( () => {
			setProcessing( false );
			setStatus( true );
			setTimeout( () => {
				setStatus( false );
			}, 2000 );
		} );
	}, [ data ] );

	const history = useHistory();
	const navigation = [
		{
			name: __( 'General Settings', 'md-wp-cli-exercise' ),
			slug: 'md_wp_cli_exercise_setting',
		},
	];

	navigation.push( {
		name: __( 'Styling', 'md-wp-cli-exercise' ),
		slug: 'md_wp_cli_exercise_styling',
	} );

	function navigate( navigateTab ) {
		setSettingsTab( navigateTab );
		history.push(
			'admin.php?page=md_wp_cli_exercise_settings&path=settings&tab=' +
				navigateTab
		);
	}

	function getSettingsTab() {
		return settingsTab || 'md_wp_cli_exercise_setting';
	}

	return (
		<form
			className="MdWpCliExerciseSettings"
			id="MdWpCliExerciseSettings"
			method="post"
		>
			<Header
				processing={ processing }
				activePage={ activePage }
				activePath={ activePath }
			/>
			<Notification status={ status } setStatus={ setStatus } />
			{ 'settings' === activePath ? (
				<Settings
					navigation={ navigation }
					tab={ tab }
					navigate={ navigate }
				/>
			) : 'feedbacks' === activePath ? (
				<Feedbacks
					navigation={ navigation }
					tab={ tab }
					navigate={ navigate }
				/>
			) : (
				<Settings
					navigation={ navigation }
					tab={ tab }
					navigate={ navigate }
				/>
			) }
		</form>
	);
}

export default Container;