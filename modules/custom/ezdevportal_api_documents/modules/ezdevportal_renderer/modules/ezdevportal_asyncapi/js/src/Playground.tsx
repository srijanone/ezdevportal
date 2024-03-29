import React, { Component } from 'react';
import AsyncApi, { ConfigInterface } from '@kyma-project/asyncapi-react';

import {
  Navigation,
  CodeEditorComponent,
  FetchSchema,
  RefreshIcon,
  Tabs,
  Tab,
  PlaygroundWrapper,
  ContentWrapper,
  CodeEditorsWrapper,
  AsyncApiWrapper,
} from './components';

import { defaultConfig, parse } from './common';

const fileUrl =  window.drupalSettings.ezdevportal_asyncapi.document || {};

interface State {
  schema: string;
  config: string;
  schemaFromEditor: string;
  schemaFromExternalResource: string;
  configFromEditor: string;
  key: number;
}

class Playground extends Component<{}, State> {

  state = {
    schema: '',
    config: defaultConfig,
    schemaFromEditor: '',
    schemaFromExternalResource: '',
    configFromEditor: defaultConfig,
    key: 1,
  };

  componentDidMount() {
    this.loadData();
  }

  loadData() {
    let array = new Uint32Array(1);
    fetch(fileUrl)
    .then(function (response) {
      if(response.ok){
        return response.text();
      }
      throw new Error('Error message.');
    })
    .then((data) => {
      this.setState({schema : data, schemaFromEditor : data, key: crypto.getRandomValues(array)});
    })
    .catch(function (err) {
      console.log("failed to load ", fileUrl, err.message);
    });
  }

  render() {

    const {
      schema,
      config = defaultConfig,
      schemaFromEditor,
      schemaFromExternalResource,
      configFromEditor,
    } = this.state;

    const parsedConfig = config
      ? parse<ConfigInterface>(config)
      : parse<ConfigInterface>(defaultConfig);

    return (
      <PlaygroundWrapper>
        <Navigation />
        <ContentWrapper>
          <CodeEditorsWrapper>
            <Tabs
              additionalHeaderContent={this.renderAdditionalHeaderContent()}
            >
              <Tab title="Schema" key="Schema">
                <>
                  <FetchSchema
                    parentCallback={this.updateSchemaFromExternalResource}
                  />
                  <CodeEditorComponent
                    key={this.state.key}
                    code={schemaFromEditor}
                    externalResource={schemaFromExternalResource}
                    parentCallback={this.updateSchema}
                    mode="text/yaml"
                  />
                </>
              </Tab>
              <Tab>
              </Tab>
            </Tabs>
          </CodeEditorsWrapper>
          <AsyncApiWrapper>
            <AsyncApi schema={schema} config={parsedConfig} />
          </AsyncApiWrapper>
        </ContentWrapper>
      </PlaygroundWrapper>
    );
  }

  private updateSchema = (schema: string) => {
    this.setState({ schemaFromEditor: schema });
  };

  private updateSchemaFromExternalResource = (schema: string) => {
    this.setState({ schemaFromExternalResource: schema });
  };

  private updateConfig = (config: string) => {
    this.setState({ configFromEditor: config });
  };

  private refreshState = () => {
    const { schemaFromEditor, configFromEditor } = this.state;
    this.setState({
      schema: schemaFromEditor,
      config: configFromEditor,
    });
  };

  private renderAdditionalHeaderContent = () => (
    <RefreshIcon onClick={this.refreshState}>{'\uE00A'}</RefreshIcon>
  );
}

export default Playground;
